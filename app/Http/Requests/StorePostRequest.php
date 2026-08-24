<?php

namespace App\Http\Requests;

use App\Enums\InstagramFormat;
use App\Enums\Platform;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'caption' => ['nullable', 'string', 'max:2200'],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => ['required', Rule::enum(Platform::class)],
            'instagram_format' => ['nullable', Rule::enum(InstagramFormat::class)],
            'publish_mode' => ['required', Rule::in(['now', 'scheduled'])],
            'scheduled_at' => ['required_if:publish_mode,scheduled', 'nullable', 'date', 'after:now'],
            'media' => ['nullable', 'array', 'max:10'],
            'media.*' => [
                'file',
                'max:102400',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/quicktime,video/webm',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'platforms.required' => 'Выберите хотя бы одну соцсеть.',
            'platforms.min' => 'Выберите хотя бы одну соцсеть.',
            'scheduled_at.required_if' => 'Укажите дату и время публикации.',
            'scheduled_at.after' => 'Время отложенной публикации должно быть в будущем.',
            'media.*.max' => 'Файл слишком большой. Максимум 100 МБ.',
            'media.*.mimetypes' => 'Можно загружать только фото и видео.',
        ];
    }

    /**
     * @return list<UploadedFile>
     */
    public function mediaFiles(): array
    {
        $uploaded = $this->file('media');

        if ($uploaded instanceof UploadedFile) {
            return [$uploaded];
        }

        if (! is_array($uploaded)) {
            return [];
        }

        return array_values($uploaded);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var list<string> $platforms */
            $platforms = $this->input('platforms', []);
            $files = $this->mediaFiles();
            $hasMedia = $files !== [];
            $caption = trim((string) $this->input('caption', ''));

            if (! $hasMedia && $caption === '') {
                $validator->errors()->add('caption', 'Добавьте текст или медиафайл.');
            }

            $hasVideo = collect($files)->contains(
                fn (UploadedFile $file): bool => str_starts_with((string) $file->getMimeType(), 'video/'),
            );

            foreach ($platforms as $platformValue) {
                $platform = Platform::tryFrom((string) $platformValue);

                if ($platform?->requiresVideo() && ! $hasVideo) {
                    $validator->errors()->add('platforms', $platform->label().' принимает только видео.');
                }
            }

            if (in_array(Platform::Instagram->value, $platforms, true)) {
                if (! $hasMedia) {
                    $validator->errors()->add('media', 'Для Instagram нужно фото или видео.');
                }

                $user = $this->user();

                if ($user instanceof User && ! $user->socialAccounts()->where('platform', Platform::Instagram)->where('is_active', true)->exists()) {
                    $validator->errors()->add('platforms', 'Сначала подключите Instagram.');
                }
            }
        });
    }
}

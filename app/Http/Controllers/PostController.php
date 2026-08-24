<?php

namespace App\Http\Controllers;

use App\Actions\Posts\CreatePost;
use App\Enums\PostStatus;
use App\Enums\TargetStatus;
use App\Http\Requests\StorePostRequest;
use App\Jobs\PublishPostJob;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        $posts = $user->posts()
            ->with(['media', 'targets.socialAccount'])
            ->latest('scheduled_at')
            ->latest('id')
            ->paginate(20)
            ->through(fn (Post $post): array => $this->postPayload($post));

        return Inertia::render('posts/index', [
            'posts' => $posts,
        ]);
    }

    public function create(Request $request): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        $accounts = $user->socialAccounts()
            ->where('is_active', true)
            ->get(['id', 'platform', 'name', 'is_sandbox']);

        return Inertia::render('posts/create', [
            'accounts' => $accounts,
        ]);
    }

    public function store(StorePostRequest $request, CreatePost $createPost): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $post = $createPost->handle($user, $request);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $post->status === PostStatus::Scheduled
                ? 'Пост запланирован.'
                : 'Пост отправлен на публикацию.',
        ]);

        return to_route('posts.show', $post);
    }

    public function show(Request $request, Post $post): Response
    {
        $this->authorize('view', $post);

        $post->load(['media', 'targets.socialAccount']);

        return Inertia::render('posts/show', [
            'post' => $this->postPayload($post),
        ]);
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->load('media');

        foreach ($post->media as $media) {
            Storage::disk($media->disk)->delete($media->path);
        }

        $post->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Пост удалён.']);

        return to_route('posts.index');
    }

    public function publish(Post $post): RedirectResponse
    {
        $this->authorize('publish', $post);

        $post->targets()->where('status', TargetStatus::Failed)->update([
            'status' => TargetStatus::Pending,
            'error_message' => null,
        ]);

        $post->status = PostStatus::Publishing;
        $post->scheduled_at = now();
        $post->save();

        PublishPostJob::dispatch($post->fresh() ?? $post);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Публикация запущена.']);

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function postPayload(Post $post): array
    {
        return [
            'id' => $post->id,
            'caption' => $post->caption,
            'status' => $post->status->value,
            'status_label' => $post->status->label(),
            'instagram_format' => $post->instagram_format?->value,
            'scheduled_at' => $post->scheduled_at?->timezone(config('app.timezone'))->format('d.m.Y H:i'),
            'scheduled_at_iso' => $post->scheduled_at?->toIso8601String(),
            'published_at' => $post->published_at?->timezone(config('app.timezone'))->format('d.m.Y H:i'),
            'created_at' => $post->created_at?->timezone(config('app.timezone'))->format('d.m.Y H:i'),
            'media' => $post->media->map(fn (PostMedia $media): array => [
                'id' => $media->id,
                'type' => $media->type->value,
                'url' => $media->publicUrl(),
                'original_name' => $media->original_name,
            ]),
            'targets' => $post->targets->map(fn ($target): array => [
                'id' => $target->id,
                'platform' => $target->platform->value,
                'platform_label' => $target->platform->label(),
                'account_name' => $target->socialAccount?->name,
                'status' => $target->status->value,
                'status_label' => $target->status->label(),
                'external_url' => $target->external_url,
                'error_message' => $target->error_message,
            ]),
        ];
    }
}

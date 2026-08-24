import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Camera, ImagePlus, Trash2 } from 'lucide-react';
import { useMemo, useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index as accountsIndex } from '@/routes/accounts';
import { store } from '@/routes/posts';
import {
    platformMeta,
    type PlatformValue,
    type SocialAccount,
} from '@/types/posting';

type Props = {
    accounts: SocialAccount[];
};

function toLocalInputValue(date: Date): string {
    const pad = (value: number) => String(value).padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

export default function CreatePost({ accounts }: Props) {
    const { sandbox } = usePage().props;
    const connected = new Set(accounts.map((account) => account.platform));
    const defaultScheduled = useMemo(() => {
        const date = new Date();
        date.setHours(date.getHours() + 1);
        date.setMinutes(0, 0, 0);

        return toLocalInputValue(date);
    }, []);

    const { data, setData, post, processing, errors, progress } = useForm({
        caption: '',
        platforms: [] as PlatformValue[],
        instagram_format: 'post',
        publish_mode: 'scheduled',
        scheduled_at: defaultScheduled,
        media: [] as File[],
    });

    const [previews, setPreviews] = useState<string[]>([]);

    const hasVideo = data.media.some((file) => file.type.startsWith('video/'));

    function togglePlatform(platform: PlatformValue, enabled: boolean) {
        setData(
            'platforms',
            enabled
                ? [...data.platforms, platform]
                : data.platforms.filter((item) => item !== platform),
        );
    }

    function addFiles(fileList: FileList | null) {
        if (!fileList) {
            return;
        }

        const next = [...data.media, ...Array.from(fileList)];
        setData('media', next);
        setPreviews(next.map((file) => URL.createObjectURL(file)));
    }

    function removeFile(index: number) {
        const next = data.media.filter((_, itemIndex) => itemIndex !== index);
        setData('media', next);
        setPreviews(next.map((file) => URL.createObjectURL(file)));
    }

    function submit(event: React.FormEvent) {
        event.preventDefault();
        post(store.url(), { forceFormData: true });
    }

    return (
        <>
            <Head title="Новый пост" />
            <form
                onSubmit={submit}
                className="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4 pb-28 md:p-6"
            >
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Новый пост
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        С телефона или компьютера: текст, фото или видео, затем
                        отметьте соцсети и время.
                    </p>
                </div>

                <section className="space-y-3">
                    <Label className="text-base">Медиа</Label>
                    <div className="grid grid-cols-2 gap-3">
                        <label className="flex min-h-24 cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border border-dashed p-4 text-sm">
                            <ImagePlus className="size-6" />
                            Галерея
                            <input
                                type="file"
                                accept="image/*,video/*"
                                multiple
                                className="sr-only"
                                onChange={(event) => addFiles(event.target.files)}
                            />
                        </label>
                        <label className="flex min-h-24 cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border border-dashed p-4 text-sm md:hidden">
                            <Camera className="size-6" />
                            Камера
                            <input
                                type="file"
                                accept="image/*,video/*"
                                capture="environment"
                                className="sr-only"
                                onChange={(event) => addFiles(event.target.files)}
                            />
                        </label>
                    </div>
                    <InputError message={errors.media} />
                    {previews.length > 0 && (
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            {data.media.map((file, index) => (
                                <div
                                    key={`${file.name}-${index}`}
                                    className="relative overflow-hidden rounded-xl border"
                                >
                                    {file.type.startsWith('video/') ? (
                                        <video
                                            src={previews[index]}
                                            className="aspect-square w-full object-cover"
                                            controls
                                            playsInline
                                        />
                                    ) : (
                                        <img
                                            src={previews[index]}
                                            alt={file.name}
                                            className="aspect-square w-full object-cover"
                                        />
                                    )}
                                    <button
                                        type="button"
                                        className="absolute top-2 right-2 rounded-full bg-black/70 p-2 text-white"
                                        onClick={() => removeFile(index)}
                                    >
                                        <Trash2 className="size-4" />
                                    </button>
                                </div>
                            ))}
                        </div>
                    )}
                </section>

                <section className="space-y-2">
                    <Label htmlFor="caption" className="text-base">
                        Текст
                    </Label>
                    <Textarea
                        id="caption"
                        value={data.caption}
                        onChange={(event) =>
                            setData('caption', event.target.value)
                        }
                        placeholder="Подпись к публикации"
                        className="min-h-36 text-base"
                    />
                    <InputError message={errors.caption} />
                </section>

                <section className="space-y-3">
                    <Label className="text-base">Куда публиковать</Label>
                    <div className="grid gap-3">
                        {(Object.keys(platformMeta) as PlatformValue[]).map(
                            (platform) => {
                                const meta = platformMeta[platform];
                                const isConnected = connected.has(platform);
                                const needsVideo =
                                    platform === 'tiktok' ||
                                    platform === 'youtube';
                                const blockedByMedia = needsVideo && !hasVideo;
                                const disabled = !isConnected || blockedByMedia;
                                const checked = data.platforms.includes(platform);

                                return (
                                    <label
                                        key={platform}
                                        className="flex min-h-16 items-start gap-3 rounded-xl border p-4 has-disabled:opacity-60"
                                    >
                                        <Checkbox
                                            className="mt-1 size-5"
                                            checked={checked}
                                            disabled={disabled}
                                            onCheckedChange={(value) =>
                                                togglePlatform(
                                                    platform,
                                                    value === true,
                                                )
                                            }
                                        />
                                        <span className="flex-1">
                                            <span className="flex items-center gap-2 font-medium">
                                                <span
                                                    className={`size-3 rounded-full ${meta.color}`}
                                                />
                                                {meta.label}
                                            </span>
                                            <span className="mt-1 block text-sm text-muted-foreground">
                                                {!isConnected
                                                    ? 'Сначала подключите аккаунт'
                                                    : blockedByMedia
                                                      ? 'Нужно видео'
                                                      : meta.hint}
                                            </span>
                                        </span>
                                    </label>
                                );
                            },
                        )}
                    </div>
                    <InputError message={errors.platforms} />
                    {accounts.length === 0 && (
                        <p className="text-sm text-muted-foreground">
                            Нет подключённых аккаунтов.{' '}
                            <Link
                                href={accountsIndex()}
                                className="underline underline-offset-4"
                            >
                                Подключить соцсети
                            </Link>
                        </p>
                    )}
                </section>

                {data.platforms.includes('instagram') && (
                    <section className="space-y-3">
                        <Label className="text-base">Формат Instagram</Label>
                        <div className="grid grid-cols-2 gap-3">
                            {(
                                [
                                    ['post', 'Пост / Reels'],
                                    ['story', 'Сторис'],
                                ] as const
                            ).map(([value, label]) => (
                                <button
                                    key={value}
                                    type="button"
                                    className={`min-h-12 rounded-xl border px-4 text-sm font-medium ${
                                        data.instagram_format === value
                                            ? 'border-primary bg-primary/5'
                                            : ''
                                    }`}
                                    onClick={() =>
                                        setData('instagram_format', value)
                                    }
                                >
                                    {label}
                                </button>
                            ))}
                        </div>
                    </section>
                )}

                <section className="space-y-3">
                    <Label className="text-base">Когда опубликовать</Label>
                    <div className="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            className={`min-h-12 rounded-xl border px-4 text-sm font-medium ${
                                data.publish_mode === 'now'
                                    ? 'border-primary bg-primary/5'
                                    : ''
                            }`}
                            onClick={() => setData('publish_mode', 'now')}
                        >
                            Сейчас
                        </button>
                        <button
                            type="button"
                            className={`min-h-12 rounded-xl border px-4 text-sm font-medium ${
                                data.publish_mode === 'scheduled'
                                    ? 'border-primary bg-primary/5'
                                    : ''
                            }`}
                            onClick={() => setData('publish_mode', 'scheduled')}
                        >
                            Отложить
                        </button>
                    </div>
                    {data.publish_mode === 'scheduled' && (
                        <div className="space-y-2">
                            <Input
                                type="datetime-local"
                                value={data.scheduled_at}
                                min={toLocalInputValue(new Date())}
                                onChange={(event) =>
                                    setData('scheduled_at', event.target.value)
                                }
                                className="min-h-12 text-base"
                            />
                            <p className="text-xs text-muted-foreground">
                                Время по Москве. Планировщик проверяет очередь
                                каждую минуту.
                            </p>
                            <InputError message={errors.scheduled_at} />
                        </div>
                    )}
                </section>

                {sandbox && (
                    <p className="rounded-xl bg-muted px-4 py-3 text-sm text-muted-foreground">
                        Сейчас включён тестовый режим: посты помечаются
                        опубликованными без реальных API. Ключи соцсетей
                        добавляются в админке и `.env`.
                    </p>
                )}

                {progress && (
                    <p className="text-sm text-muted-foreground">
                        Загрузка файлов: {progress.percentage}%
                    </p>
                )}

                <div className="sticky bottom-20 z-10 flex gap-3 bg-background/95 py-3 md:bottom-0">
                    <Button
                        type="submit"
                        size="lg"
                        className="min-h-12 flex-1"
                        disabled={processing}
                    >
                        {data.publish_mode === 'now'
                            ? 'Опубликовать'
                            : 'Запланировать'}
                    </Button>
                </div>
            </form>
        </>
    );
}

CreatePost.layout = {
    breadcrumbs: [
        { title: 'Посты', href: '/posts' },
        { title: 'Новый пост', href: '/posts/create' },
    ],
};

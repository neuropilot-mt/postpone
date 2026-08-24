import { Head, Link, router } from '@inertiajs/react';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { destroy, publish } from '@/routes/posts';
import { type PostItem } from '@/types/posting';

export default function PostShow({ post }: { post: PostItem }) {
    const canRetry = ['failed', 'partial', 'scheduled', 'publishing'].includes(
        post.status,
    );

    return (
        <>
            <Head title={`Пост #${post.id}`} />
            <div className="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div className="space-y-2">
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Пост #{post.id}
                        </h1>
                        <StatusBadge
                            status={post.status}
                            label={post.status_label}
                        />
                    </div>
                    <div className="flex flex-col gap-2 sm:flex-row">
                        {canRetry && (
                            <Button
                                className="min-h-11"
                                onClick={() =>
                                    router.post(publish.url(post.id))
                                }
                            >
                                Опубликовать сейчас
                            </Button>
                        )}
                        <Button
                            variant="destructive"
                            className="min-h-11"
                            onClick={() => {
                                if (confirm('Удалить этот пост?')) {
                                    router.delete(destroy.url(post.id));
                                }
                            }}
                        >
                            Удалить
                        </Button>
                    </div>
                </div>

                <section className="rounded-xl border p-4">
                    <h2 className="mb-2 text-sm font-medium text-muted-foreground">
                        Текст
                    </h2>
                    <p className="whitespace-pre-wrap text-sm">
                        {post.caption || 'Без текста'}
                    </p>
                    <dl className="mt-4 grid gap-2 text-sm text-muted-foreground">
                        <div>Запланирован: {post.scheduled_at || '—'}</div>
                        <div>Опубликован: {post.published_at || '—'}</div>
                        {post.instagram_format && (
                            <div>
                                Instagram:{' '}
                                {post.instagram_format === 'story'
                                    ? 'сторис'
                                    : 'пост / Reels'}
                            </div>
                        )}
                    </dl>
                </section>

                {post.media && post.media.length > 0 && (
                    <section className="grid grid-cols-2 gap-3">
                        {post.media.map((media) =>
                            media.type === 'video' ? (
                                <video
                                    key={media.id}
                                    src={media.url}
                                    controls
                                    playsInline
                                    className="aspect-square w-full rounded-xl object-cover"
                                />
                            ) : (
                                <img
                                    key={media.id}
                                    src={media.url}
                                    alt={media.original_name}
                                    className="aspect-square w-full rounded-xl object-cover"
                                />
                            ),
                        )}
                    </section>
                )}

                <section className="space-y-3">
                    <h2 className="text-lg font-semibold">Площадки</h2>
                    {post.targets?.map((target) => (
                        <div
                            key={target.id}
                            className="rounded-xl border p-4"
                        >
                            <div className="flex items-center justify-between gap-3">
                                <div>
                                    <div className="font-medium">
                                        {target.platform_label}
                                    </div>
                                    <div className="text-sm text-muted-foreground">
                                        {target.account_name}
                                    </div>
                                </div>
                                <StatusBadge
                                    status={target.status}
                                    label={target.status_label}
                                />
                            </div>
                            {target.external_url && (
                                <a
                                    href={target.external_url}
                                    className="mt-3 inline-block text-sm underline underline-offset-4"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    Открыть публикацию
                                </a>
                            )}
                            {target.error_message && (
                                <p className="mt-3 text-sm text-red-600 dark:text-red-400">
                                    {target.error_message}
                                </p>
                            )}
                        </div>
                    ))}
                </section>

                <Button asChild variant="outline">
                    <Link href="/posts">К списку</Link>
                </Button>
            </div>
        </>
    );
}

PostShow.layout = {
    breadcrumbs: [
        { title: 'Посты', href: '/posts' },
        { title: 'Просмотр', href: '#' },
    ],
};

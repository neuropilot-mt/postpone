import { Head, Link } from '@inertiajs/react';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { show as showPost } from '@/routes/posts';
import type { PostItem } from '@/types/posting';

type Paginator = {
    data: PostItem[];
    links: { url: string | null; label: string; active: boolean }[];
};

export default function AdminPosts({ posts }: { posts: Paginator }) {
    return (
        <>
            <Head title="Все посты" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Все посты
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Очередь публикаций всех пользователей.
                    </p>
                </div>

                <div className="grid gap-3">
                    {posts.data.map((post) => (
                        <Link
                            key={post.id}
                            href={showPost(post.id)}
                            className="rounded-xl border p-4 hover:bg-muted/40"
                        >
                            <div className="flex items-start justify-between gap-3">
                                <div>
                                    <p className="line-clamp-2 text-sm">
                                        {post.caption || 'Без текста'}
                                    </p>
                                    <p className="mt-1 text-xs text-muted-foreground">
                                        {post.author} · {post.scheduled_at}
                                    </p>
                                </div>
                                <StatusBadge
                                    status={post.status}
                                    label={post.status_label}
                                />
                            </div>
                        </Link>
                    ))}
                </div>

                <div className="flex flex-wrap gap-2">
                    {posts.links.map((link) => (
                        <Button
                            key={link.label}
                            asChild={Boolean(link.url)}
                            variant={link.active ? 'default' : 'outline'}
                            size="sm"
                            disabled={!link.url}
                        >
                            {link.url ? (
                                <Link
                                    href={link.url}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            ) : (
                                <span
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            )}
                        </Button>
                    ))}
                </div>
            </div>
        </>
    );
}

AdminPosts.layout = {
    breadcrumbs: [
        { title: 'Админка', href: '/admin' },
        { title: 'Посты', href: '/admin/posts' },
    ],
};

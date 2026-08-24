import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { create as createPost, show as showPost } from '@/routes/posts';
import {
    platformMeta,
    type PlatformValue,
    type PostItem,
} from '@/types/posting';

type Paginator = {
    data: PostItem[];
    links: { url: string | null; label: string; active: boolean }[];
};

export default function PostsIndex({ posts }: { posts: Paginator }) {
    return (
        <>
            <Head title="Посты" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Посты
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Очередь публикаций, сторис и роликов.
                        </p>
                    </div>
                    <Button asChild size="lg" className="min-h-12 w-full sm:w-auto">
                        <Link href={createPost()}>
                            <Plus className="size-4" />
                            Новый пост
                        </Link>
                    </Button>
                </div>

                {posts.data.length === 0 ? (
                    <div className="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground">
                        Постов ещё нет.
                    </div>
                ) : (
                    <div className="grid gap-3">
                        {posts.data.map((post) => (
                            <Link
                                key={post.id}
                                href={showPost(post.id)}
                                className="rounded-xl border p-4 hover:bg-muted/40"
                            >
                                <div className="flex items-start justify-between gap-3">
                                    <p className="line-clamp-2 text-sm">
                                        {post.caption || 'Без текста'}
                                    </p>
                                    <StatusBadge
                                        status={post.status}
                                        label={post.status_label}
                                    />
                                </div>
                                <div className="mt-3 flex flex-wrap gap-2 text-xs text-muted-foreground">
                                    {post.scheduled_at && (
                                        <span>{post.scheduled_at}</span>
                                    )}
                                    {post.targets?.map((target) => (
                                        <span
                                            key={target.id}
                                            className="rounded-md bg-muted px-1.5 py-0.5"
                                        >
                                            {platformMeta[
                                                target.platform as PlatformValue
                                            ]?.label ?? target.platform_label}
                                        </span>
                                    ))}
                                </div>
                            </Link>
                        ))}
                    </div>
                )}

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

PostsIndex.layout = {
    breadcrumbs: [{ title: 'Посты', href: '/posts' }],
};

import { Head, Link } from '@inertiajs/react';
import { Plus, SquarePen } from 'lucide-react';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { create as createPost } from '@/routes/posts';
import { index as accounts } from '@/routes/accounts';
import { show as showPost } from '@/routes/posts';
import { platformMeta, type PlatformValue, type PostItem } from '@/types/posting';

type Props = {
    stats: {
        scheduled: number;
        published: number;
        failed: number;
        accounts: number;
    };
    connected_platforms: PlatformValue[];
    upcoming: PostItem[];
    recent: PostItem[];
};

export default function Dashboard({
    stats,
    connected_platforms,
    upcoming,
    recent,
}: Props) {
    return (
        <>
            <Head title="Лента" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Отложка
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Создайте пост, отметьте соцсети и поставьте время.
                        </p>
                    </div>
                    <Button asChild size="lg" className="min-h-12 w-full sm:w-auto">
                        <Link href={createPost()}>
                            <Plus className="size-4" />
                            Новый пост
                        </Link>
                    </Button>
                </div>

                <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <Stat title="Запланировано" value={stats.scheduled} />
                    <Stat title="Опубликовано" value={stats.published} />
                    <Stat title="С ошибками" value={stats.failed} />
                    <Stat title="Аккаунтов" value={stats.accounts} />
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Куда публикуем</CardTitle>
                        <CardDescription>
                            Instagram, VK, TikTok и YouTube Shorts. Подключите
                            аккаунты, затем выбирайте их чекбоксами в посте.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-2">
                        {(
                            Object.keys(platformMeta) as PlatformValue[]
                        ).map((platform) => {
                            const connected =
                                connected_platforms.includes(platform);

                            return (
                                <div
                                    key={platform}
                                    className="flex items-center justify-between rounded-xl border p-4"
                                >
                                    <div className="flex items-center gap-3">
                                        <span
                                            className={`size-9 rounded-lg ${platformMeta[platform].color}`}
                                        />
                                        <div>
                                            <div className="font-medium">
                                                {platformMeta[platform].label}
                                            </div>
                                            <div className="text-xs text-muted-foreground">
                                                {connected
                                                    ? 'Подключено'
                                                    : platformMeta[platform]
                                                          .hint}
                                            </div>
                                        </div>
                                    </div>
                                    {!connected && (
                                        <Button asChild size="sm" variant="outline">
                                            <Link href={accounts()}>
                                                Подключить
                                            </Link>
                                        </Button>
                                    )}
                                </div>
                            );
                        })}
                    </CardContent>
                </Card>

                <section className="space-y-3">
                    <div className="flex items-center justify-between">
                        <h2 className="text-lg font-semibold">Ближайшие</h2>
                        <Button asChild variant="ghost" size="sm">
                            <Link href="/posts">
                                <SquarePen className="size-4" />
                                Все посты
                            </Link>
                        </Button>
                    </div>
                    {upcoming.length === 0 ? (
                        <p className="rounded-xl border border-dashed p-6 text-sm text-muted-foreground">
                            Пока нет отложенных публикаций.
                        </p>
                    ) : (
                        <div className="grid gap-3">
                            {upcoming.map((post) => (
                                <PostRow key={post.id} post={post} />
                            ))}
                        </div>
                    )}
                </section>

                <section className="space-y-3">
                    <h2 className="text-lg font-semibold">Недавние</h2>
                    {recent.length === 0 ? (
                        <div className="rounded-xl border border-dashed p-6 text-sm text-muted-foreground">
                            <p>Создайте первый пост с телефона или компьютера.</p>
                            <Button asChild className="mt-4">
                                <Link href={createPost()}>Создать</Link>
                            </Button>
                        </div>
                    ) : (
                        <div className="grid gap-3">
                            {recent.map((post) => (
                                <PostRow key={post.id} post={post} />
                            ))}
                        </div>
                    )}
                </section>
            </div>
        </>
    );
}

function Stat({ title, value }: { title: string; value: number }) {
    return (
        <Card className="py-4">
            <CardHeader className="px-4">
                <CardDescription>{title}</CardDescription>
                <CardTitle className="text-3xl">{value}</CardTitle>
            </CardHeader>
        </Card>
    );
}

function PostRow({ post }: { post: PostItem }) {
    return (
        <Link
            href={showPost(post.id)}
            className="flex items-start justify-between gap-3 rounded-xl border p-4 transition-colors hover:bg-muted/40"
        >
            <div className="min-w-0 space-y-2">
                <p className="line-clamp-2 text-sm">
                    {post.caption || 'Без текста'}
                </p>
                <div className="flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                    {post.scheduled_at && <span>{post.scheduled_at}</span>}
                    {post.platforms?.map((platform) => (
                        <span key={platform} className="rounded-md bg-muted px-1.5 py-0.5">
                            {platformMeta[platform as PlatformValue]?.label ??
                                platform}
                        </span>
                    ))}
                </div>
            </div>
            <StatusBadge status={post.status} label={post.status_label} />
        </Link>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Лента',
            href: '/dashboard',
        },
    ],
};

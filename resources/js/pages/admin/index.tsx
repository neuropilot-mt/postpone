import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { posts as adminPosts, users as adminUsers } from '@/routes/admin';

type Props = {
    stats: {
        users: number;
        posts: number;
        accounts: number;
    };
    sandbox: boolean;
    oauth: { platform: string; label: string; configured: boolean }[];
};

export default function AdminIndex({ stats, sandbox, oauth }: Props) {
    return (
        <>
            <Head title="Админка" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Админка
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Пользователи, очередь постов и статус интеграций.
                    </p>
                </div>

                <div className="grid gap-3 sm:grid-cols-3">
                    <Stat title="Пользователи" value={stats.users} />
                    <Stat title="Посты" value={stats.posts} />
                    <Stat title="Аккаунты" value={stats.accounts} />
                </div>

                <div className="flex flex-col gap-3 sm:flex-row">
                    <Button asChild className="min-h-11">
                        <Link href={adminUsers()}>Пользователи</Link>
                    </Button>
                    <Button asChild variant="outline" className="min-h-11">
                        <Link href={adminPosts()}>Все посты</Link>
                    </Button>
                </div>

                <section className="rounded-xl border p-4">
                    <h2 className="font-semibold">Публикация</h2>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Тестовый режим:{' '}
                        {sandbox
                            ? 'включён'
                            : 'выключен, идут живые API'}
                        . Меняется через SOCIAL_SANDBOX в `.env`.
                    </p>
                    <div className="mt-4 grid gap-2">
                        {oauth.map((item) => (
                            <div
                                key={item.platform}
                                className="flex items-center justify-between text-sm"
                            >
                                <span>{item.label}</span>
                                <span className="text-muted-foreground">
                                    {item.configured
                                        ? 'ключи заданы'
                                        : 'ключи не заданы'}
                                </span>
                            </div>
                        ))}
                    </div>
                </section>
            </div>
        </>
    );
}

function Stat({ title, value }: { title: string; value: number }) {
    return (
        <div className="rounded-xl border p-4">
            <div className="text-sm text-muted-foreground">{title}</div>
            <div className="text-3xl font-semibold">{value}</div>
        </div>
    );
}

AdminIndex.layout = {
    breadcrumbs: [{ title: 'Админка', href: '/admin' }],
};

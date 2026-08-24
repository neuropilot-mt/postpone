import { Head, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { toggleAdmin } from '@/routes/admin/users';

type AdminUser = {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
    posts_count: number;
    accounts_count: number;
    created_at: string | null;
};

export default function AdminUsers({ users }: { users: AdminUser[] }) {
    return (
        <>
            <Head title="Пользователи" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Пользователи
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Первый зарегистрированный пользователь становится
                        администратором.
                    </p>
                </div>

                <div className="grid gap-3">
                    {users.map((user) => (
                        <div
                            key={user.id}
                            className="flex flex-col gap-3 rounded-xl border p-4 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div>
                                <div className="font-medium">{user.name}</div>
                                <div className="text-sm text-muted-foreground">
                                    {user.email}
                                </div>
                                <div className="mt-1 text-xs text-muted-foreground">
                                    Постов: {user.posts_count} · Аккаунтов:{' '}
                                    {user.accounts_count}
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <span className="text-sm">
                                    {user.is_admin ? 'Админ' : 'Пользователь'}
                                </span>
                                <Button
                                    variant="outline"
                                    className="min-h-11"
                                    onClick={() =>
                                        router.patch(toggleAdmin.url(user.id))
                                    }
                                >
                                    Сменить роль
                                </Button>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}

AdminUsers.layout = {
    breadcrumbs: [
        { title: 'Админка', href: '/admin' },
        { title: 'Пользователи', href: '/admin/users' },
    ],
};

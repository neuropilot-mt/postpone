import { Head, router, useForm } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { destroy, store } from '@/routes/accounts';
import { redirect } from '@/routes/oauth';
import {
    platformMeta,
    type PlatformValue,
    type SocialAccount,
} from '@/types/posting';

type PlatformOption = {
    value: PlatformValue;
    label: string;
    requires_video: boolean;
    oauth_configured: boolean;
};

type Props = {
    accounts: SocialAccount[];
    platforms: PlatformOption[];
    sandbox: boolean;
};

export default function AccountsIndex({
    accounts,
    platforms,
    sandbox,
}: Props) {
    const form = useForm({
        platform: 'instagram' as PlatformValue,
        name: '',
    });

    return (
        <>
            <Head title="Соцсети" />
            <div className="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4 md:p-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Соцсети
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Подключите Instagram, VK, TikTok и YouTube. Пока нет
                        ключей API — можно добавить тестовые аккаунты
                        {sandbox ? ' (сейчас включён sandbox).' : '.'}
                    </p>
                </div>

                <div className="grid gap-3">
                    {platforms.map((platform) => (
                        <div
                            key={platform.value}
                            className="flex flex-col gap-3 rounded-xl border p-4 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div className="flex items-center gap-3">
                                <span
                                    className={`size-9 rounded-lg ${platformMeta[platform.value].color}`}
                                />
                                <div>
                                    <div className="font-medium">
                                        {platform.label}
                                    </div>
                                    <div className="text-xs text-muted-foreground">
                                        {platform.oauth_configured
                                            ? 'OAuth настроен'
                                            : 'OAuth ключи ещё не заданы'}
                                    </div>
                                </div>
                            </div>
                            {platform.oauth_configured ? (
                                <Button
                                    asChild
                                    className="min-h-11 w-full sm:w-auto"
                                >
                                    <a href={redirect.url(platform.value)}>
                                        Подключить через OAuth
                                    </a>
                                </Button>
                            ) : (
                                <Button
                                    className="min-h-11 w-full sm:w-auto"
                                    disabled
                                >
                                    Подключить через OAuth
                                </Button>
                            )}
                        </div>
                    ))}
                </div>

                    <form
                        className="space-y-4 rounded-xl border p-4"
                        onSubmit={(event) => {
                            event.preventDefault();
                            form.post(store.url(), { preserveScroll: true });
                        }}
                    >
                        <h2 className="font-semibold">Тестовый аккаунт</h2>
                        <p className="text-sm text-muted-foreground">
                            Для проверки композера и отложки без живых API.
                        </p>
                        <div className="space-y-2">
                            <Label htmlFor="platform">Площадка</Label>
                            <select
                                id="platform"
                                className="border-input h-12 w-full rounded-md border bg-transparent px-3 text-base"
                                value={form.data.platform}
                                onChange={(event) =>
                                    form.setData(
                                        'platform',
                                        event.target.value as PlatformValue,
                                    )
                                }
                            >
                                {platforms.map((platform) => (
                                    <option
                                        key={platform.value}
                                        value={platform.value}
                                    >
                                        {platform.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="name">Название</Label>
                            <Input
                                id="name"
                                className="min-h-12"
                                value={form.data.name}
                                onChange={(event) =>
                                    form.setData('name', event.target.value)
                                }
                                placeholder="Мой Instagram"
                            />
                            <InputError message={form.errors.name} />
                        </div>
                        <Button
                            type="submit"
                            className="min-h-11 w-full sm:w-auto"
                            disabled={form.processing}
                        >
                            Добавить тестовый аккаунт
                        </Button>
                    </form>

                <section className="space-y-3">
                    <h2 className="text-lg font-semibold">Подключено</h2>
                    {accounts.length === 0 ? (
                        <p className="rounded-xl border border-dashed p-6 text-sm text-muted-foreground">
                            Пока нет аккаунтов.
                        </p>
                    ) : (
                        accounts.map((account) => (
                            <div
                                key={account.id}
                                className="flex flex-col gap-3 rounded-xl border p-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div>
                                    <div className="font-medium">
                                        {account.name}
                                    </div>
                                    <div className="text-sm text-muted-foreground">
                                        {account.platform_label}
                                        {account.is_sandbox
                                            ? ' · тест'
                                            : ''}
                                    </div>
                                </div>
                                <Button
                                    variant="outline"
                                    className="min-h-11"
                                    onClick={() =>
                                        router.delete(destroy.url(account.id))
                                    }
                                >
                                    Отключить
                                </Button>
                            </div>
                        ))
                    )}
                </section>
            </div>
        </>
    );
}

AccountsIndex.layout = {
    breadcrumbs: [{ title: 'Соцсети', href: '/accounts' }],
};

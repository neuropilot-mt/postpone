import { Head, Link, usePage } from '@inertiajs/react';
import { dashboard, login, privacy, register, terms } from '@/routes';

export default function Welcome() {
    const { auth } = usePage().props;

    return (
        <>
            <Head title="Отложенные публикации" />
            <div className="flex min-h-screen flex-col bg-background text-foreground">
                <header className="mx-auto flex w-full max-w-5xl items-center justify-between p-4 md:p-6">
                    <span className="font-semibold">Posting</span>
                    <nav className="flex gap-2">
                        {auth.user ? (
                            <Link
                                href={dashboard()}
                                className="rounded-md border px-4 py-2 text-sm"
                            >
                                Открыть
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={login()}
                                    className="rounded-md px-4 py-2 text-sm"
                                >
                                    Войти
                                </Link>
                                <Link
                                    href={register()}
                                    className="rounded-md border px-4 py-2 text-sm"
                                >
                                    Регистрация
                                </Link>
                            </>
                        )}
                    </nav>
                </header>

                <main className="mx-auto flex w-full max-w-5xl flex-1 flex-col justify-center gap-8 px-4 py-10 md:px-6">
                    <div className="max-w-2xl space-y-4">
                        <h1 className="text-4xl font-semibold tracking-tight md:text-5xl">
                            Отложка для Instagram, VK, TikTok и YouTube Shorts
                        </h1>
                        <p className="text-lg text-muted-foreground">
                            Создайте пост с телефона или компьютера: текст,
                            фото, видео. Отметьте соцсети чекбоксами и
                            запланируйте публикацию.
                        </p>
                        <div className="flex flex-col gap-3 sm:flex-row">
                            <Link
                                href={auth.user ? dashboard() : register()}
                                className="inline-flex min-h-12 items-center justify-center rounded-md bg-primary px-5 text-sm font-medium text-primary-foreground"
                            >
                                Начать
                            </Link>
                            <Link
                                href={login()}
                                className="inline-flex min-h-12 items-center justify-center rounded-md border px-5 text-sm font-medium"
                            >
                                У меня уже есть аккаунт
                            </Link>
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-3">
                        <Feature
                            title="Один пост — несколько сетей"
                            text="Instagram пост или сторис, стена VK, TikTok и YouTube Shorts."
                        />
                        <Feature
                            title="С телефона и с компьютера"
                            text="Большие кнопки, камера, галерея и загрузка файлов с диска."
                        />
                        <Feature
                            title="Отложенный постинг"
                            text="Поставьте дату и время — очередь опубликует сама."
                        />
                    </div>
                </main>

                <footer className="mx-auto flex w-full max-w-5xl flex-wrap gap-4 p-4 text-sm text-muted-foreground md:p-6">
                    <Link href={privacy()} className="hover:text-foreground">
                        Политика конфиденциальности
                    </Link>
                    <Link href={terms()} className="hover:text-foreground">
                        Пользовательское соглашение
                    </Link>
                </footer>
            </div>
        </>
    );
}

function Feature({ title, text }: { title: string; text: string }) {
    return (
        <div className="rounded-xl border p-5">
            <h2 className="font-medium">{title}</h2>
            <p className="mt-2 text-sm text-muted-foreground">{text}</p>
        </div>
    );
}

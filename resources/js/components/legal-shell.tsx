import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { home } from '@/routes';

export function LegalShell({
    title,
    children,
}: {
    title: string;
    children: ReactNode;
}) {
    return (
        <div className="flex min-h-screen flex-col bg-background text-foreground">
            <header className="mx-auto flex w-full max-w-3xl items-center justify-between p-4 md:p-6">
                <Link href={home()} className="font-semibold">
                    Posting
                </Link>
                <Link
                    href={home()}
                    className="rounded-md border px-4 py-2 text-sm"
                >
                    На главную
                </Link>
            </header>
            <main className="mx-auto w-full max-w-3xl flex-1 space-y-6 px-4 pb-16 md:px-6">
                <h1 className="text-3xl font-semibold tracking-tight">
                    {title}
                </h1>
                <div className="space-y-6 text-sm leading-relaxed text-muted-foreground [&_ul]:list-disc [&_ul]:space-y-2 [&_ul]:pl-5">
                    {children}
                </div>
            </main>
        </div>
    );
}

export function LegalSection({
    title,
    children,
}: {
    title: string;
    children: ReactNode;
}) {
    return (
        <section className="space-y-3">
            <h2 className="text-base font-medium text-foreground">{title}</h2>
            {children}
        </section>
    );
}

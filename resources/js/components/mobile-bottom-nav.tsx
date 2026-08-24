import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid, Plus, Share2, SquarePen } from 'lucide-react';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';
import { index as accounts } from '@/routes/accounts';
import { create as createPost, index as posts } from '@/routes/posts';

const items = [
    { title: 'Лента', href: dashboard(), icon: LayoutGrid },
    { title: 'Посты', href: posts(), icon: SquarePen },
    { title: 'Новый', href: createPost(), icon: Plus, primary: true },
    { title: 'Сети', href: accounts(), icon: Share2 },
];

export function MobileBottomNav() {
    const { isCurrentUrl } = useCurrentUrl();
    const { auth } = usePage().props;

    if (!auth.user) {
        return null;
    }

    return (
        <nav className="fixed inset-x-0 bottom-0 z-50 border-t bg-background/95 pb-[env(safe-area-inset-bottom)] backdrop-blur md:hidden">
            <ul className="grid grid-cols-4">
                {items.map((item) => {
                    const active = isCurrentUrl(item.href);

                    return (
                        <li key={item.title}>
                            <Link
                                href={item.href}
                                className={cn(
                                    'flex min-h-14 flex-col items-center justify-center gap-1 px-2 text-[11px] font-medium',
                                    active
                                        ? 'text-foreground'
                                        : 'text-muted-foreground',
                                )}
                            >
                                <span
                                    className={cn(
                                        'flex size-9 items-center justify-center rounded-full',
                                        item.primary &&
                                            'bg-primary text-primary-foreground',
                                        !item.primary &&
                                            active &&
                                            'bg-muted text-foreground',
                                    )}
                                >
                                    <item.icon className="size-5" />
                                </span>
                                {item.title}
                            </Link>
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
}

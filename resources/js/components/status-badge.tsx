import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

const variants: Record<string, string> = {
    scheduled: 'border-transparent bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-200',
    publishing: 'border-transparent bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200',
    published: 'border-transparent bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200',
    partial: 'border-transparent bg-orange-100 text-orange-800 dark:bg-orange-950 dark:text-orange-200',
    failed: 'border-transparent bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-200',
    draft: 'border-transparent bg-muted text-muted-foreground',
    pending: 'border-transparent bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-200',
};

export function StatusBadge({
    status,
    label,
}: {
    status: string;
    label: string;
}) {
    return (
        <Badge
            variant="outline"
            className={cn('font-medium', variants[status] ?? variants.draft)}
        >
            {label}
        </Badge>
    );
}

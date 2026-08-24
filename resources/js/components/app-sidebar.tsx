import { Link, usePage } from '@inertiajs/react';
import {
    LayoutGrid,
    Plus,
    Settings2,
    Share2,
    SquarePen,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as admin } from '@/routes/admin';
import { index as accounts } from '@/routes/accounts';
import { create as createPost, index as posts } from '@/routes/posts';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Лента',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Посты',
        href: posts(),
        icon: SquarePen,
    },
    {
        title: 'Новый пост',
        href: createPost(),
        icon: Plus,
    },
    {
        title: 'Соцсети',
        href: accounts(),
        icon: Share2,
    },
];

export function AppSidebar() {
    const { auth } = usePage().props;
    const items = auth.user?.is_admin
        ? [
              ...mainNavItems,
              {
                  title: 'Админка',
                  href: admin(),
                  icon: Settings2,
              } satisfies NavItem,
          ]
        : mainNavItems;

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={items} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}

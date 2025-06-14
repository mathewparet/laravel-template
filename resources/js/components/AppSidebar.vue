<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, SwatchBook, ShoppingBag, Layers, Banknote, CircleUser } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { usePage } from '@inertiajs/vue3';
import { Config } from '@/types/config';
import { NavItem, GroupedNavItem } from '@/types/index';
import { computed } from 'vue';

const mainNavItems: GroupedNavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        name: 'dashboard',
        icon: LayoutGrid,
        group: 'Portal'
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];

const navGroups: Array<string> = computed<Array<string>>(() => [...new Set(mainNavItems.map((item: GroupedNavItem) => item.group))])

const groupedNavItems = mainNavItems.reduce<Record<string, GroupedNavItem[]>>((acc, item) => {
  if (!acc[item.group]) {
    acc[item.group] = [];
  }
  acc[item.group].push(item);
  return acc;
}, {});

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain v-for="(items, group) in groupedNavItems" :key="group" :items="items" :group="group" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

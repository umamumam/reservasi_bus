<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid, Bus, Route, Calendar, Ticket, Home, MapPin, CalendarClock } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
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
import type { NavItem } from '@/types';
import { computed } from 'vue';

const page = usePage<any>();

const mainNavItems = computed<NavItem[]>(() => {
    const role = page.props.auth?.user?.role || 'user';
    const items: NavItem[] = [
        {
            title: 'Kembali ke Web',
            href: '/',
            icon: Home,
        },
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (role === 'admin') {
        items.push(
            { title: 'Armada Bus', href: '/admin/buses', icon: Bus },
            { title: 'Manajemen Kota', href: '/admin/cities', icon: MapPin },
            { title: 'Rute', href: '/admin/routes', icon: Route },
            { title: 'Jadwal', href: '/admin/schedules', icon: CalendarClock },
            { title: 'Data Transaksi', href: '/admin/bookings', icon: BookOpen }
        );
    } else {
        items.push({ title: 'Tiket Saya', href: '/user/bookings', icon: Ticket });
    }

    return items;
});

const footerNavItems: NavItem[] = [];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

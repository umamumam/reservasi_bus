<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Bell, ChevronDown, ChevronsUpDown, LogOut, Settings, User as UserIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const user = computed(() => page.props.auth.user);
const { getInitials } = useInitials();

const handleLogout = () => {
    // router.flushAll() handled by Inertia link
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon" class="relative text-muted-foreground hover:text-foreground">
                <Bell class="h-5 w-5" />
                <span class="absolute top-2 right-2.5 h-2 w-2 rounded-full bg-red-500 shadow-[0_0_0_2px_var(--bg-background)]"></span>
            </Button>

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" class="h-12 px-2 flex items-center gap-3 rounded-full bg-[#e84c59] hover:bg-[#d43f4c] text-white hover:text-white transition-colors border-none">
                        <Avatar class="h-9 w-9 shrink-0 bg-white items-center justify-center">
                            <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                            <AvatarFallback class="bg-transparent text-[#e84c59]">
                                <UserIcon class="h-5 w-5" />
                            </AvatarFallback>
                        </Avatar>
                        <div class="flex flex-col items-start justify-center pr-2">
                            <span class="text-[10px] font-black tracking-widest uppercase opacity-90">{{ user.role === 'admin' ? 'ADMIN' : 'MEMBER' }}</span>
                            <span class="text-sm font-bold leading-none truncate max-w-[120px]">{{ user.name }}</span>
                        </div>
                        <ChevronDown class="h-4 w-4 opacity-80 shrink-0 mr-1" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-56" align="end">
                    <DropdownMenuLabel class="font-normal">
                        <div class="flex flex-col space-y-1">
                            <p class="text-sm font-medium leading-none">{{ user.name }}</p>
                            <p class="text-xs leading-none text-muted-foreground">
                                {{ user.email }}
                            </p>
                        </div>
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuGroup>
                        <DropdownMenuItem as-child>
                            <Link :href="edit()" class="flex w-full items-center">
                                <Settings class="mr-2 h-4 w-4" />
                                <span>Settings</span>
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuGroup>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem as-child>
                        <Link :href="logout()" method="post" as="button" class="flex w-full items-center text-destructive">
                            <LogOut class="mr-2 h-4 w-4" />
                            <span>Log out</span>
                        </Link>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>

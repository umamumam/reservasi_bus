<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import {
    Users,
    Search,
    Download,
    ChevronRight,
    ArrowUpDown,
    UserCircle,
    Mail,
    Phone,
    ShieldCheck,
    UserCog,
} from 'lucide-vue-next';

import { confirmDelete, notifySuccess } from '@/utils/sweetalert';

const props = defineProps<{ users: any[] }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Manajemen User', href: '/admin/users' },
        ],
    },
});

const searchQuery = ref('');

const filteredUsers = computed(() => {
    if (!searchQuery.value) return props.users;
    const query = searchQuery.value.toLowerCase();
    return props.users.filter(
        (user) =>
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query) ||
            (user.phone && user.phone.includes(query)),
    );
});

const deleteUser = async (user: any) => {
    if (user.role === 'admin') {
        notifySuccess('Opps!', 'Admin tidak bisa dihapus dari sistem ini.');
        return;
    }
    const confirmed = await confirmDelete(
        'Hapus User?',
        `Yakin ingin menghapus user ${user.name}? Semua data booking user ini juga akan terhapus.`,
    );
    if (confirmed) {
        useForm({}).delete(`/admin/users/${user.id}`, {
            onSuccess: () =>
                notifySuccess('Deleted!', 'User berhasil dihapus dari sistem.'),
        });
    }
};

const formatRole = (role: string) => {
    return role.charAt(0).toUpperCase() + role.slice(1);
};
</script>

<template>
    <Head title="Visionary Data Center - Users" />
    <div
        class="flex h-full flex-1 flex-col gap-8 bg-[#fdfdfd] p-4 sm:p-8 dark:bg-[#050505]"
    >
        <!-- Visionary Header -->
        <header
            class="flex flex-col justify-between gap-6 md:flex-row md:items-center"
            data-aos="fade-down"
        >
            <div>
                <div class="mb-1 flex items-center gap-2">
                    <span
                        class="rounded bg-blue-500/10 px-2 py-0.5 text-[10px] font-black tracking-[0.2em] text-blue-500 uppercase"
                        >Security & Identity</span
                    >
                    <span
                        class="h-1 w-1 rounded-full bg-gray-300 dark:bg-white/20"
                    ></span>
                    <span
                        class="text-[10px] font-bold tracking-widest text-gray-400 uppercase"
                        >User Registry</span
                    >
                </div>
                <h1
                    class="flex items-center gap-3 text-4xl font-black tracking-tight text-gray-900 dark:text-white"
                >
                    Manajemen User
                    <span
                        class="rounded-full bg-gray-100 px-3 py-1 text-sm font-bold text-gray-500 dark:bg-white/5"
                        >{{ filteredUsers.length }}</span
                    >
                </h1>
            </div>
        </header>

        <!-- Search Bar -->
        <div
            class="flex flex-col items-center justify-between gap-4 rounded-[2.5rem] border border-gray-100 bg-white p-2 shadow-sm sm:flex-row dark:border-white/10 dark:bg-white/5"
            data-aos="fade-up"
        >
            <div class="group relative w-full sm:max-w-xl">
                <Search
                    class="absolute top-1/2 left-6 h-4 w-4 -translate-y-1/2 text-gray-400 transition group-focus-within:text-[#d4af37]"
                />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama, email, atau telepon..."
                    class="w-full border-none bg-transparent py-4 pr-6 pl-14 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-0 dark:text-white"
                />
            </div>
            <div class="flex items-center gap-3 pr-2">
                <button
                    class="flex items-center gap-2 px-4 py-2 text-[10px] font-black tracking-widest text-gray-400 uppercase transition hover:text-gray-900"
                >
                    <Download class="h-4 w-4" /> Export
                </button>
            </div>
        </div>

        <!-- Visionary Table -->
        <div
            class="overflow-hidden rounded-[3rem] border border-gray-100 bg-white shadow-sm dark:border-white/10 dark:bg-white/5"
            data-aos="fade-up"
            data-aos-delay="100"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr
                            class="border-b border-gray-50 bg-gray-50/30 dark:border-white/5 dark:bg-black/20"
                        >
                            <th class="w-24 px-8 py-6">
                                <div
                                    class="flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-gray-400 uppercase"
                                >
                                    User ID <ArrowUpDown class="h-3 w-3" />
                                </div>
                            </th>
                            <th class="px-8 py-6">
                                <div
                                    class="text-[10px] font-black tracking-[0.2em] text-gray-400 uppercase"
                                >
                                    User Profile
                                </div>
                            </th>
                            <th class="px-8 py-6">
                                <div
                                    class="text-[10px] font-black tracking-[0.2em] text-gray-400 uppercase"
                                >
                                    Role & Status
                                </div>
                            </th>
                            <th class="px-8 py-6 text-right">
                                <div
                                    class="text-[10px] font-black tracking-[0.2em] text-gray-400 uppercase"
                                >
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                        <tr
                            v-for="user in filteredUsers"
                            :key="user.id"
                            class="group transition hover:bg-gray-50/50 dark:hover:bg-white/10"
                        >
                            <td
                                class="px-8 py-8 font-mono text-xs font-black text-gray-400"
                            >
                                #{{ String(user.id).padStart(3, '0') }}
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-blue-500 transition duration-500 group-hover:bg-blue-500 group-hover:text-white dark:bg-white/10"
                                    >
                                        <UserCircle class="h-6 w-6" />
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-black tracking-tight text-gray-900 uppercase dark:text-white"
                                        >
                                            {{ user.name }}
                                        </div>
                                        <div
                                            class="mt-1 flex items-center gap-3"
                                        >
                                            <div
                                                class="flex items-center gap-1 text-[10px] font-bold tracking-widest text-gray-400 uppercase"
                                            >
                                                <Mail class="h-3 w-3" />
                                                {{ user.email }}
                                            </div>
                                            <div
                                                v-if="user.phone"
                                                class="flex items-center gap-1 text-[10px] font-bold tracking-widest text-gray-400 uppercase"
                                            >
                                                <Phone class="h-3 w-3" />
                                                {{ user.phone }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-3">
                                    <div
                                        :class="[
                                            'flex items-center gap-1.5 rounded-lg px-3 py-1 text-[10px] font-black tracking-widest uppercase',
                                            user.role === 'admin'
                                                ? 'bg-emerald-500/10 text-emerald-500'
                                                : 'bg-blue-500/10 text-blue-500',
                                        ]"
                                    >
                                        <ShieldCheck
                                            v-if="user.role === 'admin'"
                                            class="h-3 w-3"
                                        />
                                        <UserCog v-else class="h-3 w-3" />
                                        {{ formatRole(user.role) }}
                                    </div>
                                    <div
                                        v-if="user.email_verified_at"
                                        class="flex items-center gap-1 text-[10px] font-black tracking-widest text-emerald-500 uppercase"
                                    >
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                        ></div>
                                        Verified
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8 text-right">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <button
                                        v-if="user.role !== 'admin'"
                                        @click="deleteUser(user)"
                                        class="group/btn rounded-2xl bg-gray-50 p-3 text-gray-400 shadow-inner transition hover:bg-red-500/10 hover:text-red-500 dark:bg-white/5"
                                    >
                                        <Trash2
                                            class="h-4 w-4 transition group-hover/btn:scale-110"
                                        />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredUsers.length === 0">
                            <td colspan="4" class="px-8 py-32 text-center">
                                <div
                                    class="flex flex-col items-center justify-center"
                                >
                                    <Users
                                        class="mb-4 h-16 w-16 text-gray-200"
                                    />
                                    <h3
                                        class="text-xl font-black tracking-tight text-gray-900 uppercase dark:text-white"
                                    >
                                        No Records Found
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        The user database is currently empty or
                                        doesn't match your search criteria.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="flex items-center justify-between border-t border-gray-50 bg-gray-50/30 px-8 py-6 dark:border-white/5 dark:bg-black/20"
            >
                <span
                    class="text-[10px] font-black tracking-widest text-gray-400 uppercase"
                    >Total Records: {{ filteredUsers.length }} Users</span
                >
                <div class="flex items-center gap-2">
                    <button
                        class="rounded-xl border border-gray-100 bg-white p-2 text-gray-400 disabled:opacity-50 dark:border-white/10 dark:bg-white/5"
                        disabled
                    >
                        <ChevronRight class="h-4 w-4 rotate-180" />
                    </button>
                    <button
                        class="h-8 w-8 rounded-xl bg-gray-900 text-[10px] font-black text-white dark:bg-white dark:text-black"
                    >
                        1
                    </button>
                    <button
                        class="rounded-xl border border-gray-100 bg-white p-2 text-gray-400 disabled:opacity-50 dark:border-white/10 dark:bg-white/5"
                        disabled
                    >
                        <ChevronRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

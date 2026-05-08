<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import {
    MapPin,
    Plus,
    X,
    Edit,
    Trash2,
    Search,
    Download,
    ChevronRight,
    ArrowUpDown,
    Navigation,
} from 'lucide-vue-next';

import { confirmDelete, notifySuccess } from '@/utils/sweetalert';

const props = defineProps<{ cities: any[] }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Manajemen Kota', href: '/admin/cities' },
        ],
    },
});

const showModal = ref(false);
const isEditing = ref(false);
const searchQuery = ref('');

const filteredCities = computed(() => {
    if (!searchQuery.value) return props.cities;
    const query = searchQuery.value.toLowerCase();
    return props.cities.filter((city) =>
        city.name.toLowerCase().includes(query),
    );
});

const form = useForm({
    id: null,
    name: '',
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    showModal.value = true;
};

const openEditModal = (city: any) => {
    form.clearErrors();
    isEditing.value = true;
    form.id = city.id;
    form.name = city.name;
    showModal.value = true;
};

const saveCity = () => {
    if (isEditing.value) {
        form.put(`/admin/cities/${form.id}`, {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess('Updated!', 'Data kota berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/cities', {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess('Created!', 'Kota baru berhasil didaftarkan.');
            },
        });
    }
};

const deleteCity = async (city: any) => {
    const confirmed = await confirmDelete(
        'Hapus Kota?',
        `Yakin ingin menghapus kota ${city.name}? Rute yang menggunakan kota ini juga bisa ikut terhapus.`,
    );
    if (confirmed) {
        useForm({}).delete(`/admin/cities/${city.id}`, {
            onSuccess: () =>
                notifySuccess('Deleted!', 'Kota berhasil dihapus dari sistem.'),
        });
    }
};
</script>

<template>
    <Head title="Visionary Data Center - Kota" />
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
                        class="rounded bg-emerald-500/10 px-2 py-0.5 text-[10px] font-black tracking-[0.2em] text-emerald-500 uppercase"
                        >Regional Management</span
                    >
                    <span
                        class="h-1 w-1 rounded-full bg-gray-300 dark:bg-white/20"
                    ></span>
                    <span
                        class="text-[10px] font-bold tracking-widest text-gray-400 uppercase"
                        >Network Nodes</span
                    >
                </div>
                <h1
                    class="flex items-center gap-3 text-4xl font-black tracking-tight text-gray-900 dark:text-white"
                >
                    Manajemen Kota
                    <span
                        class="rounded-full bg-gray-100 px-3 py-1 text-sm font-bold text-gray-500 dark:bg-white/5"
                        >{{ filteredCities.length }}</span
                    >
                </h1>
            </div>

            <button
                @click="openCreateModal"
                class="flex items-center gap-2 rounded-3xl bg-gray-900 px-8 py-4 text-xs font-black tracking-widest text-white uppercase shadow-xl shadow-gray-200 transition-all hover:scale-105 active:scale-95 dark:bg-white dark:text-black dark:shadow-none"
            >
                <Plus class="h-4 w-4" /> Tambah Kota
            </button>
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
                    placeholder="Cari nama kota..."
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
                                    Node ID <ArrowUpDown class="h-3 w-3" />
                                </div>
                            </th>
                            <th class="px-8 py-6">
                                <div
                                    class="text-[10px] font-black tracking-[0.2em] text-gray-400 uppercase"
                                >
                                    City Name
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
                            v-for="city in filteredCities"
                            :key="city.id"
                            class="group transition hover:bg-gray-50/50 dark:hover:bg-white/10"
                        >
                            <td
                                class="px-8 py-8 font-mono text-xs font-black text-gray-400"
                            >
                                #{{ String(city.id).padStart(3, '0') }}
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-emerald-500 transition duration-500 group-hover:bg-emerald-500 group-hover:text-white dark:bg-white/10"
                                    >
                                        <MapPin class="h-6 w-6" />
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-black tracking-tight text-gray-900 uppercase dark:text-white"
                                        >
                                            {{ city.name }}
                                        </div>
                                        <div
                                            class="mt-1 text-[10px] font-bold tracking-widest text-gray-400 uppercase"
                                        >
                                            Operational Station
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8 text-right">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <button
                                        @click="openEditModal(city)"
                                        class="group/btn rounded-2xl bg-gray-50 p-3 text-gray-400 shadow-inner transition hover:bg-blue-500/10 hover:text-blue-500 dark:bg-white/5"
                                    >
                                        <Edit
                                            class="h-4 w-4 transition group-hover/btn:scale-110"
                                        />
                                    </button>
                                    <button
                                        @click="deleteCity(city)"
                                        class="group/btn rounded-2xl bg-gray-50 p-3 text-gray-400 shadow-inner transition hover:bg-red-500/10 hover:text-red-500 dark:bg-white/5"
                                    >
                                        <Trash2
                                            class="h-4 w-4 transition group-hover/btn:scale-110"
                                        />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredCities.length === 0">
                            <td colspan="3" class="px-8 py-32 text-center">
                                <div
                                    class="flex flex-col items-center justify-center"
                                >
                                    <Navigation
                                        class="mb-4 h-16 w-16 text-gray-200"
                                    />
                                    <h3
                                        class="text-xl font-black tracking-tight text-gray-900 uppercase dark:text-white"
                                    >
                                        No Operational Nodes Found
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        The location data is currently empty or
                                        doesn't match your query.
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
                    >Network Coverage: {{ filteredCities.length }} Cities</span
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

        <!-- Polished Modal -->
        <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
            >
                <div
                    class="fixed inset-0 bg-black/60 backdrop-blur-md"
                    @click="showModal = false"
                ></div>

                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-12 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-12 scale-95"
                >
                    <div
                        class="relative w-full max-w-sm overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-2xl dark:border-white/10 dark:bg-[#050505]"
                    >
                        <div
                            class="border-b border-gray-50 px-10 py-8 dark:border-white/5"
                        >
                            <h3
                                class="flex items-center gap-3 text-2xl font-black tracking-tight text-gray-900 uppercase dark:text-white"
                            >
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-500"
                                >
                                    <MapPin class="h-5 w-5" />
                                </div>
                                {{ isEditing ? 'Edit Node' : 'New Node' }}
                            </h3>
                        </div>

                        <form @submit.prevent="saveCity" class="space-y-8 p-10">
                            <div class="group">
                                <label
                                    class="mb-3 ml-1 block text-[10px] font-black tracking-widest text-gray-400 uppercase transition group-focus-within:text-emerald-500"
                                    >Location Name</label
                                >
                                <input
                                    type="text"
                                    v-model="form.name"
                                    required
                                    placeholder="Contoh: Jakarta"
                                    class="w-full rounded-xl border-none bg-gray-50 px-6 py-4 text-sm font-bold text-gray-900 shadow-inner transition placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 dark:bg-white/5 dark:text-white"
                                />
                            </div>

                            <div class="flex items-center gap-4 pt-4">
                                <button
                                    type="button"
                                    @click="showModal = false"
                                    class="flex-1 px-8 py-4 text-[10px] font-black tracking-[0.2em] text-gray-400 uppercase transition hover:text-gray-600"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="flex-[2] rounded-xl bg-gray-900 px-8 py-4 text-[10px] font-black tracking-[0.2em] text-white uppercase shadow-xl transition-all hover:scale-105 active:scale-95 disabled:opacity-50 dark:bg-white dark:text-black"
                                >
                                    {{
                                        form.processing ? 'Syncing...' : 'Save'
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </transition>
            </div>
        </transition>
    </div>
</template>

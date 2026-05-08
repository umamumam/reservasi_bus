<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { 
    Route, 
    Plus, 
    X, 
    Edit, 
    Trash2, 
    Search, 
    Download, 
    ChevronRight, 
    ArrowUpDown,
    MapPin,
    ArrowRight
} from 'lucide-vue-next';

import { confirmDelete, notifySuccess } from '@/utils/sweetalert';

const props = defineProps<{ 
    routes: any[];
    cities: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Manajemen Rute', href: '/admin/routes' },
        ],
    },
});

const showModal = ref(false);
const isEditing = ref(false);
const searchQuery = ref('');

const filteredRoutes = computed(() => {
    if (!searchQuery.value) return props.routes;
    const query = searchQuery.value.toLowerCase();
    return props.routes.filter(route => 
        route.origin_city.name.toLowerCase().includes(query) || 
        route.destination_city.name.toLowerCase().includes(query)
    );
});

const form = useForm({
    id: null,
    origin_city_id: '',
    destination_city_id: '',
    distance: 0,
    base_price: 0,
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    showModal.value = true;
};

const openEditModal = (route: any) => {
    form.clearErrors();
    isEditing.value = true;
    form.id = route.id;
    form.origin_city_id = route.origin_city_id;
    form.destination_city_id = route.destination_city_id;
    form.distance = route.distance;
    form.base_price = route.base_price;
    showModal.value = true;
};

const saveRoute = () => {
    if (isEditing.value) {
        form.put(`/admin/routes/${form.id}`, {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess('Updated!', 'Data rute berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/routes', {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess('Created!', 'Rute baru berhasil didaftarkan.');
            },
        });
    }
};

const deleteRoute = async (route: any) => {
    const confirmed = await confirmDelete('Hapus Rute?', `Anda akan menghapus rute ${route.origin_city.name} ke ${route.destination_city.name}.`);
    if (confirmed) {
        useForm({}).delete(`/admin/routes/${route.id}`, {
            onSuccess: () => notifySuccess('Deleted!', 'Rute berhasil dihapus dari sistem.'),
        });
    }
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(price);
};
</script>

<template>
    <Head title="Visionary Data Center - Rute" />
    <div class="flex h-full flex-1 flex-col gap-8 p-4 sm:p-8 bg-[#fdfdfd] dark:bg-[#050505]">
        
        <!-- Visionary Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6" data-aos="fade-down">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-500 text-[10px] font-black uppercase tracking-[0.2em]">Logistics Architecture</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/20"></span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Global Travel Vectors</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                    Manajemen Rute
                    <span class="text-sm font-bold px-3 py-1 rounded-full bg-gray-100 dark:bg-white/5 text-gray-500">{{ filteredRoutes.length }}</span>
                </h1>
            </div>
            
            <button @click="openCreateModal" class="bg-gray-900 dark:bg-white text-white dark:text-black px-8 py-4 rounded-3xl font-black text-xs uppercase tracking-widest shadow-xl shadow-gray-200 dark:shadow-none hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                <Plus class="w-4 h-4" /> Tambah Rute
            </button>
        </header>

        <!-- Search Bar -->
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between p-2 rounded-[2.5rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 shadow-sm" data-aos="fade-up">
            <div class="relative w-full sm:max-w-xl group">
                <Search class="absolute left-6 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#d4af37] transition" />
                <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Cari rute (kota asal atau tujuan)..." 
                    class="w-full bg-transparent border-none focus:ring-0 pl-14 pr-6 py-4 text-sm font-bold text-gray-900 dark:text-white placeholder:text-gray-400"
                />
            </div>
            <div class="flex items-center gap-3 pr-2">
                <button class="flex items-center gap-2 px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition">
                    <Download class="w-4 h-4" /> Export
                </button>
            </div>
        </div>

        <!-- Visionary Table -->
        <div class="bg-white dark:bg-white/5 rounded-[3rem] shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-gray-50 dark:border-white/5 bg-gray-50/30 dark:bg-black/20">
                            <th class="px-8 py-6">
                                <div class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Origin Station <ArrowUpDown class="w-3 h-3" />
                                </div>
                            </th>
                            <th class="px-8 py-6">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Destination</div>
                            </th>
                            <th class="px-8 py-6">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Scale / Distance</div>
                            </th>
                            <th class="px-8 py-6">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Base Valuation</div>
                            </th>
                            <th class="px-8 py-6 text-right">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Actions</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                        <tr v-for="route in filteredRoutes" :key="route.id" class="hover:bg-gray-50/50 dark:hover:bg-white/10 transition group">
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-3 text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                    {{ route.origin_city.name }}
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-3 text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                    <div class="w-2 h-2 rounded-full bg-[#d4af37]"></div>
                                    {{ route.destination_city.name }}
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-black text-gray-900 dark:text-white">{{ route.distance }} KM</div>
                                    <ArrowRight class="w-3 h-3 text-gray-300" />
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <div class="text-sm font-black text-gray-900 dark:text-white font-mono">{{ formatPrice(route.base_price) }}</div>
                            </td>
                            <td class="px-8 py-8 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal(route)" class="p-3 rounded-2xl bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-blue-500 hover:bg-blue-500/10 transition group/btn shadow-inner">
                                        <Edit class="w-4 h-4 group-hover/btn:scale-110 transition" />
                                    </button>
                                    <button @click="deleteRoute(route)" class="p-3 rounded-2xl bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition group/btn shadow-inner">
                                        <Trash2 class="w-4 h-4 group-hover/btn:scale-110 transition" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredRoutes.length === 0">
                            <td colspan="5" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <Route class="w-16 h-16 text-gray-200 mb-4" />
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">No Logistics Routes Identified</h3>
                                    <p class="text-gray-500 text-sm mt-2">Adjust your vector parameters or register a new route path.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-8 py-6 bg-gray-50/30 dark:bg-black/20 border-t border-gray-50 dark:border-white/5 flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Active Corridors: {{ filteredRoutes.length }} Rutes</span>
                <div class="flex items-center gap-2">
                    <button class="p-2 rounded-xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 text-gray-400 disabled:opacity-50" disabled>
                        <ChevronRight class="w-4 h-4 rotate-180" />
                    </button>
                    <button class="w-8 h-8 rounded-xl bg-gray-900 text-white dark:bg-white dark:text-black text-[10px] font-black">1</button>
                    <button class="p-2 rounded-xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 text-gray-400 disabled:opacity-50" disabled>
                        <ChevronRight class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Polished Modal -->
        <transition 
            enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" 
            leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-md" @click="showModal = false"></div>
                
                <transition 
                    enter-active-class="ease-out duration-300" enter-from-class="opacity-0 translate-y-12 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100" 
                    leave-active-class="ease-in duration-200" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 translate-y-12 scale-95">
                    <div class="relative bg-white dark:bg-[#050505] rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 dark:border-white/10">
                        <div class="px-10 py-8 border-b border-gray-50 dark:border-white/5">
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                                    <Route class="w-5 h-5" />
                                </div>
                                {{ isEditing ? 'Edit Route Path' : 'New Route Path' }}
                            </h3>
                        </div>
                        
                        <form @submit.prevent="saveRoute" class="p-10 space-y-8">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-blue-500 transition">Origin Node</label>
                                    <select v-model="form.origin_city_id" required class="w-full bg-gray-50 dark:bg-white/5 border-none rounded-xl px-6 py-4 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition shadow-inner appearance-none">
                                        <option value="" disabled>Pilih Asal</option>
                                        <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                                    </select>
                                </div>
                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-[#d4af37] transition">Destination Node</label>
                                    <select v-model="form.destination_city_id" required class="w-full bg-gray-50 dark:bg-white/5 border-none rounded-xl px-6 py-4 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-[#d4af37] transition shadow-inner appearance-none">
                                        <option value="" disabled>Pilih Tujuan</option>
                                        <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Path Distance (KM)</label>
                                    <input type="number" v-model="form.distance" required min="1" 
                                        class="w-full bg-gray-50 dark:bg-white/5 border-none rounded-xl px-6 py-4 text-sm font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition shadow-inner font-mono">
                                </div>
                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Base Pricing (IDR)</label>
                                    <input type="number" v-model="form.base_price" required min="0" 
                                        class="w-full bg-gray-50 dark:bg-white/5 border-none rounded-xl px-6 py-4 text-sm font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition shadow-inner font-mono">
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-4">
                                <button type="button" @click="showModal = false" class="flex-1 px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-gray-600 transition">Cancel</button>
                                <button type="submit" :disabled="form.processing" class="flex-[2] px-8 py-4 rounded-xl bg-gray-900 text-white dark:bg-white dark:text-black text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover:scale-105 active:scale-95 transition-all disabled:opacity-50">
                                    {{ form.processing ? 'Syncing...' : 'Register Route' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </transition>
            </div>
        </transition>

    </div>
</template>

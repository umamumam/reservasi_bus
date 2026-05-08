<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { 
    Bus, 
    Plus, 
    X, 
    Edit, 
    Trash2, 
    Search, 
    Download, 
    ChevronRight, 
    ArrowUpDown,
    CheckCircle2
} from 'lucide-vue-next';

import { confirmDelete, notifySuccess } from '@/utils/sweetalert';

const props = defineProps<{ buses: any[] }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Manajemen Armada', href: '/admin/buses' },
        ],
    },
});

const showModal = ref(false);
const isEditing = ref(false);
const searchQuery = ref('');

const filteredBuses = computed(() => {
    if (!searchQuery.value) return props.buses;
    const query = searchQuery.value.toLowerCase();
    return props.buses.filter(bus => 
        bus.name.toLowerCase().includes(query) || 
        bus.plate_number.toLowerCase().includes(query)
    );
});

const form = useForm({
    id: null,
    name: '',
    plate_number: '',
    layout_type: '2-2',
    total_seats: 30,
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    showModal.value = true;
};

const openEditModal = (bus: any) => {
    form.clearErrors();
    isEditing.value = true;
    form.id = bus.id;
    form.name = bus.name;
    form.plate_number = bus.plate_number;
    form.layout_type = bus.layout_type;
    form.total_seats = bus.total_seats;
    showModal.value = true;
};

const saveBus = () => {
    if (isEditing.value) {
        form.put(`/admin/buses/${form.id}`, {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess('Updated!', 'Data armada berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/buses', {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess('Created!', 'Armada baru berhasil didaftarkan.');
            },
        });
    }
};

const deleteBus = async (bus: any) => {
    const confirmed = await confirmDelete('Hapus Armada?', `Anda akan menghapus armada ${bus.name}. Tindakan ini tidak dapat dibatalkan.`);
    if (confirmed) {
        useForm({}).delete(`/admin/buses/${bus.id}`, {
            onSuccess: () => notifySuccess('Deleted!', 'Armada berhasil dihapus dari sistem.'),
        });
    }
};
</script>

<template>
    <Head title="Visionary Data Center - Armada" />
    <div class="flex h-full flex-1 flex-col gap-8 p-4 sm:p-8 bg-[#fdfdfd] dark:bg-[#050505]">
        
        <!-- Visionary Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6" data-aos="fade-down">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-500 text-[10px] font-black uppercase tracking-[0.2em]">Fleet Management</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/20"></span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Global Transport Assets</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                    Manajemen Armada
                    <span class="text-sm font-bold px-3 py-1 rounded-full bg-gray-100 dark:bg-white/5 text-gray-500">{{ filteredBuses.length }}</span>
                </h1>
            </div>
            
            <button @click="openCreateModal" class="bg-gray-900 dark:bg-white text-white dark:text-black px-8 py-4 rounded-3xl font-black text-xs uppercase tracking-widest shadow-xl shadow-gray-200 dark:shadow-none hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                <Plus class="w-4 h-4" /> Tambah Armada
            </button>
        </header>

        <!-- Search Bar -->
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between p-2 rounded-[2.5rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 shadow-sm" data-aos="fade-up">
            <div class="relative w-full sm:max-w-xl group">
                <Search class="absolute left-6 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#d4af37] transition" />
                <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Cari nama bus atau plat nomor..." 
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
                                    Asset Name <ArrowUpDown class="w-3 h-3" />
                                </div>
                            </th>
                            <th class="px-8 py-6">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Registration</div>
                            </th>
                            <th class="px-8 py-6">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Configuration</div>
                            </th>
                            <th class="px-8 py-6">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Capacity</div>
                            </th>
                            <th class="px-8 py-6 text-right">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Actions</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                        <tr v-for="bus in filteredBuses" :key="bus.id" class="hover:bg-gray-50/50 dark:hover:bg-white/10 transition group">
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/10 flex items-center justify-center text-[#d4af37] group-hover:bg-[#d4af37] group-hover:text-white transition duration-500">
                                        <Bus class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 dark:text-white tracking-tight">{{ bus.name }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">HDEX Fleet Unit</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8 font-mono text-xs font-bold text-gray-600 dark:text-gray-400 tracking-tighter uppercase">
                                <span class="px-3 py-1 rounded bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                                    {{ bus.plate_number }}
                                </span>
                            </td>
                            <td class="px-8 py-8">
                                <span class="px-3 py-1.5 text-[9px] font-black uppercase tracking-[0.2em] rounded-full border border-[#d4af37]/20 bg-[#d4af37]/5 text-[#d4af37]">
                                    {{ bus.layout_type === 'sleeper' ? 'Sleeper Class' : 'Executive ' + bus.layout_type }}
                                </span>
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-black text-gray-900 dark:text-white">{{ bus.total_seats }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Seats Available</div>
                                </div>
                            </td>
                            <td class="px-8 py-8 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal(bus)" class="p-3 rounded-2xl bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-blue-500 hover:bg-blue-500/10 transition group/btn shadow-inner">
                                        <Edit class="w-4 h-4 group-hover/btn:scale-110 transition" />
                                    </button>
                                    <button @click="deleteBus(bus)" class="p-3 rounded-2xl bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition group/btn shadow-inner">
                                        <Trash2 class="w-4 h-4 group-hover/btn:scale-110 transition" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredBuses.length === 0">
                            <td colspan="5" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <Bus class="w-16 h-16 text-gray-200 mb-4" />
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">No Fleet Assets Found</h3>
                                    <p class="text-gray-500 text-sm mt-2">Try adjusting your search criteria or add a new bus.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-8 py-6 bg-gray-50/30 dark:bg-black/20 border-t border-gray-50 dark:border-white/5 flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inventory Index: {{ filteredBuses.length }} Units</span>
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
                                <div class="w-10 h-10 rounded-xl bg-[#d4af37]/10 flex items-center justify-center text-[#d4af37]">
                                    <Bus class="w-5 h-5" />
                                </div>
                                {{ isEditing ? 'Edit Asset' : 'New Asset' }}
                            </h3>
                        </div>
                        
                        <form @submit.prevent="saveBus" class="p-10 space-y-8">
                            <div class="space-y-6">
                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-[#d4af37] transition">Asset Identity</label>
                                    <input type="text" v-model="form.name" required placeholder="Fleet Name" 
                                        class="w-full bg-gray-50 dark:bg-white/5 border-none rounded-xl px-6 py-4 text-sm font-bold text-gray-900 dark:text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#d4af37] transition shadow-inner">
                                </div>
                                
                                <div class="group">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-[#d4af37] transition">Registration Number</label>
                                    <input type="text" v-model="form.plate_number" required placeholder="B 1234 XYZ" 
                                        class="w-full bg-gray-50 dark:bg-white/5 border-none rounded-xl px-6 py-4 text-sm font-black text-gray-900 dark:text-white placeholder:text-gray-400 focus:ring-2 focus:ring-[#d4af37] transition shadow-inner uppercase font-mono">
                                </div>

                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Class Configuration</label>
                                        <select v-model="form.layout_type" required class="w-full bg-gray-50 dark:bg-white/5 border-none rounded-xl px-6 py-4 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-[#d4af37] transition shadow-inner appearance-none">
                                            <option value="2-2">Executive 2-2</option>
                                            <option value="sleeper">Sleeper Class</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Total Capacity</label>
                                        <input type="number" v-model="form.total_seats" required min="1" 
                                            class="w-full bg-gray-50 dark:bg-white/5 border-none rounded-xl px-6 py-4 text-sm font-black text-gray-900 dark:text-white focus:ring-2 focus:ring-[#d4af37] transition shadow-inner font-mono">
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-4">
                                <button type="button" @click="showModal = false" class="flex-1 px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-gray-600 transition">Cancel</button>
                                <button type="submit" :disabled="form.processing" class="flex-[2] px-8 py-4 rounded-xl bg-gray-900 text-white dark:bg-white dark:text-black text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover:scale-105 active:scale-95 transition-all disabled:opacity-50">
                                    {{ form.processing ? 'Processing...' : 'Secure Transaction' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </transition>
            </div>
        </transition>

    </div>
</template>

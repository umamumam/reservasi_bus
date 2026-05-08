<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { 
    Search, 
    Filter, 
    Eye, 
    ArrowUpDown, 
    MoreHorizontal,
    Download,
    Calendar,
    ChevronRight,
    CheckCircle2,
    Clock
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{ bookings: any[] }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Data Transaksi', href: '/admin/bookings' },
        ],
    },
});

const searchQuery = ref('');
const statusFilter = ref('all');

const filteredBookings = computed(() => {
    let results = props.bookings;
    
    if (statusFilter.value !== 'all') {
        results = results.filter(b => b.status === statusFilter.value);
    }
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        results = results.filter(booking => {
            return booking.booking_code.toLowerCase().includes(query) ||
                   booking.user.name.toLowerCase().includes(query) ||
                   booking.schedule?.bus_route?.origin_city?.name.toLowerCase().includes(query) ||
                   booking.schedule?.bus_route?.destination_city?.name.toLowerCase().includes(query) ||
                   booking.schedule?.bus?.name.toLowerCase().includes(query);
        });
    }
    
    return results;
});

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(price);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Visionary Data Center - Transaksi" />
    <div class="flex h-full flex-1 flex-col gap-8 p-4 sm:p-8 bg-[#fdfdfd] dark:bg-[#050505]">
        
        <!-- Visionary Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6" data-aos="fade-down">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-500 text-[10px] font-black uppercase tracking-[0.2em]">Data Repository</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/20"></span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Master Transaction Records</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                    Arsip Transaksi
                    <span class="text-sm font-bold px-3 py-1 rounded-full bg-gray-100 dark:bg-white/5 text-gray-500">{{ filteredBookings.length }}</span>
                </h1>
            </div>
            
            <div class="flex items-center gap-3">
                <button class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest hover:shadow-md transition">
                    <Download class="w-4 h-4 text-[#d4af37]" />
                    Export CSV
                </button>
            </div>
        </header>

        <!-- Search & Control Bar (DataTables Style Responsive) -->
        <div class="flex flex-col lg:flex-row gap-4 items-center justify-between p-2 rounded-[2.5rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 shadow-sm" data-aos="fade-up">
            <div class="relative w-full lg:max-w-xl group">
                <Search class="absolute left-6 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#d4af37] transition" />
                <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Cari kode booking, nama customer, rute, atau bus..." 
                    class="w-full bg-transparent border-none focus:ring-0 pl-14 pr-6 py-4 text-sm font-bold text-gray-900 dark:text-white placeholder:text-gray-400"
                />
            </div>
            <div class="flex items-center gap-2 pr-2 w-full lg:w-auto overflow-x-auto no-scrollbar">
                <div class="flex items-center bg-gray-50 dark:bg-white/5 rounded-2xl p-1 shrink-0">
                    <button 
                        @click="statusFilter = 'all'"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition"
                        :class="statusFilter === 'all' ? 'bg-white dark:bg-white/10 text-gray-900 dark:text-white shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                    >All</button>
                    <button 
                        @click="statusFilter = 'paid'"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition"
                        :class="statusFilter === 'paid' ? 'bg-white dark:bg-white/10 text-green-500 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                    >Paid</button>
                    <button 
                        @click="statusFilter = 'pending'"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition"
                        :class="statusFilter === 'pending' ? 'bg-white dark:bg-white/10 text-yellow-500 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                    >Pending</button>
                </div>
                <div class="h-8 w-[1px] bg-gray-100 dark:bg-white/10 mx-2 shrink-0"></div>
                <button class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-gray-900 text-white dark:bg-white dark:text-black text-[10px] font-black uppercase tracking-widest shrink-0">
                    <Filter class="w-3 h-3" /> Filters
                </button>
            </div>
        </div>

        <!-- Visionary Table Container -->
        <div class="bg-white dark:bg-white/5 rounded-[3rem] shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-50 dark:border-white/5 bg-gray-50/30 dark:bg-black/20">
                            <th class="px-8 py-6">
                                <div class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Booking Details <ArrowUpDown class="w-3 h-3" />
                                </div>
                            </th>
                            <th class="px-8 py-6">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Customer</div>
                            </th>
                            <th class="px-8 py-6">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Itinerary</div>
                            </th>
                            <th class="px-8 py-6">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Financials</div>
                            </th>
                            <th class="px-8 py-6 text-center">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Verification</div>
                            </th>
                            <th class="px-8 py-6 text-right">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                        <tr v-for="booking in filteredBookings" :key="booking.id" class="hover:bg-gray-50/50 dark:hover:bg-white/10 transition group">
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-900 text-white flex items-center justify-center font-black font-mono text-xs shadow-lg group-hover:scale-110 transition duration-500 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-[#d4af37] opacity-0 group-hover:opacity-100 transition duration-500"></div>
                                        <span class="relative z-10">{{ booking.booking_code.slice(-2) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 dark:text-white tracking-widest">{{ booking.booking_code }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter flex items-center gap-1 mt-1">
                                            <Calendar class="w-3 h-3" /> {{ formatDate(booking.created_at) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500 font-black text-xs">
                                        {{ booking.user.name.charAt(0) }}
                                    </div>
                                    <div class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ booking.user.name }}</div>
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900 dark:text-white">{{ booking.schedule?.bus_route?.origin_city?.name }} &rarr; {{ booking.schedule?.bus_route?.destination_city?.name }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] px-2 py-0.5 rounded bg-[#d4af37]/10 text-[#d4af37] font-black uppercase tracking-widest">{{ booking.schedule?.bus?.name }}</span>
                                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ booking.seats?.length }} SEATS</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <div class="text-sm font-black text-gray-900 dark:text-white">{{ formatPrice(booking.total_price) }}</div>
                                <div class="text-[10px] font-bold text-green-500 uppercase tracking-widest flex items-center gap-1">
                                    <CheckCircle2 class="w-2 h-2" /> Calculated
                                </div>
                            </td>
                            <td class="px-8 py-8 text-center">
                                <span class="px-4 py-1.5 text-[9px] font-black uppercase tracking-[0.2em] rounded-full inline-block"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-500/20': booking.status === 'paid',
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 border border-yellow-500/20': booking.status === 'pending',
                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-500/20': booking.status === 'cancelled',
                                    }">
                                    {{ booking.status === 'paid' ? 'VERIFIED' : booking.status }}
                                </span>
                            </td>
                            <td class="px-8 py-8 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="`/booking/${booking.id}/ticket`" class="p-3 rounded-2xl bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-[#d4af37] hover:bg-[#d4af37]/10 transition group/btn shadow-inner">
                                        <Eye class="w-4 h-4 group-hover/btn:scale-110 transition" />
                                    </Link>
                                    <button class="p-3 rounded-2xl bg-gray-50 dark:bg-white/5 text-gray-400 hover:text-gray-900 dark:hover:text-white transition shadow-inner">
                                        <MoreHorizontal class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredBookings.length === 0">
                            <td colspan="6" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 rounded-[2rem] bg-gray-50 dark:bg-white/5 flex items-center justify-center text-gray-300 mb-4">
                                        <Search class="w-8 h-8" />
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Data Tidak Ditemukan</h3>
                                    <p class="text-gray-500 text-sm mt-2">Tidak ada transaksi yang cocok dengan kata kunci "{{ searchQuery }}"</p>
                                    <button @click="searchQuery = ''; statusFilter = 'all'" class="mt-6 text-[10px] font-black text-[#d4af37] uppercase tracking-[0.2em] hover:underline">Clear All Filters</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Visionary Pagination Simulation -->
            <div class="px-8 py-6 bg-gray-50/30 dark:bg-black/20 border-t border-gray-50 dark:border-white/5 flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Showing {{ filteredBookings.length }} of {{ bookings.length }} total records</span>
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
    </div>
</template>


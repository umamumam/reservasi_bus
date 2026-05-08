<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Search, 
    Ticket, 
    ChevronRight, 
    Clock, 
    CheckCircle2, 
    XCircle,
    ArrowRight,
    Filter
} from 'lucide-vue-next';

import PublicLayout from '@/layouts/PublicLayout.vue';

const props = defineProps<{ bookings: any[] }>();

defineOptions({
    layout: PublicLayout
});

const isUserMenuOpen = ref(false);
const searchQuery = ref('');
const statusFilter = ref('all');

const filteredBookings = computed(() => {
    return props.bookings.filter(booking => {
        const matchesSearch = booking.booking_code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            booking.schedule.bus_route.origin_city.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            booking.schedule.bus_route.destination_city.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesStatus = statusFilter.value === 'all' || booking.status === statusFilter.value;
        
        return matchesSearch && matchesStatus;
    });
});

const formatTime = (timeStr: string) => {
    return new Date(timeStr).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
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
    <Head title="Tiket Saya | HDEX Trans" />

    <div class="bg-[#fdfdfd] text-gray-900 font-sans">
        <main class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-12">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 rounded bg-[#d4af37]/10 text-[#d4af37] text-[10px] font-black uppercase tracking-widest">Transaction History</span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">Riwayat Pesanan Tiket</h1>
                <p class="text-gray-500 mt-2 text-lg">Kelola dan pantau seluruh tiket perjalanan Anda dalam satu dashboard.</p>
            </div>

            <!-- Enhanced Search & Filter Bar -->
            <div class="flex flex-col md:flex-row gap-4 mb-10 items-center justify-between p-2 rounded-3xl bg-white border border-gray-100 shadow-sm">
                <div class="relative w-full md:max-w-xl group">
                    <Search class="absolute left-6 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#d4af37] transition" />
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Cari kode booking atau kota tujuan..." 
                        class="w-full bg-transparent border-none focus:ring-0 pl-14 pr-6 py-4 text-sm font-bold text-gray-900 placeholder:text-gray-400"
                    />
                </div>
                <div class="flex items-center gap-2 pr-2 w-full md:w-auto">
                    <div class="flex bg-gray-50 rounded-full p-1 w-full md:w-auto">
                        <button 
                            @click="statusFilter = 'all'"
                            :class="statusFilter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                            class="px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest transition"
                        >
                            All
                        </button>
                        <button 
                            @click="statusFilter = 'paid'"
                            :class="statusFilter === 'paid' ? 'bg-white text-green-600 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                            class="px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest transition"
                        >
                            Paid
                        </button>
                        <button 
                            @click="statusFilter = 'pending'"
                            :class="statusFilter === 'pending' ? 'bg-white text-amber-600 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                            class="px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest transition"
                        >
                            Pending
                        </button>
                    </div>
                </div>
            </div>
            
            <div v-if="filteredBookings.length === 0" class="rounded-3xl border border-dashed border-gray-200 bg-white p-20 text-center shadow-sm">
                <div class="w-24 h-24 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-8 text-gray-300">
                    <Ticket class="w-12 h-12" />
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">Data Tidak Ditemukan</h3>
                <p class="text-gray-500 mb-10 max-w-sm mx-auto text-sm font-medium">Kami tidak menemukan riwayat pesanan yang sesuai dengan kriteria pencarian Anda.</p>
                <Link href="/#beranda" class="inline-flex items-center gap-3 rounded-full bg-[#d4af37] px-10 py-4 text-[10px] font-black text-white uppercase tracking-widest shadow-xl hover:scale-105 transition-all">
                    Pesan Tiket Baru <ArrowRight class="w-4 h-4" />
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 gap-8">
                <div v-for="booking in filteredBookings" :key="booking.id" class="rounded-3xl border border-gray-50 bg-white overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 group border-b-4 border-b-transparent hover:border-b-[#d4af37]">
                    <div class="bg-gray-50/50 px-8 py-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm text-gray-400 group-hover:text-[#d4af37] transition-colors">
                                <Ticket class="w-6 h-6" />
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-0.5">Booking Reference</span>
                                <div class="font-mono font-black text-2xl text-gray-900 tracking-tighter">{{ booking.booking_code }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div v-if="booking.status === 'paid'" class="flex items-center gap-2 px-5 py-2 rounded-2xl bg-green-500/10 text-green-600">
                                <CheckCircle2 class="w-4 h-4" />
                                <span class="text-[10px] font-black uppercase tracking-[0.2em]">Validated</span>
                            </div>
                            <div v-else-if="booking.status === 'pending'" class="flex items-center gap-2 px-5 py-2 rounded-2xl bg-amber-500/10 text-amber-600">
                                <Clock class="w-4 h-4" />
                                <span class="text-[10px] font-black uppercase tracking-[0.2em]">Waiting</span>
                            </div>
                            <div v-else class="flex items-center gap-2 px-5 py-2 rounded-2xl bg-red-500/10 text-red-600">
                                <XCircle class="w-4 h-4" />
                                <span class="text-[10px] font-black uppercase tracking-[0.2em]">Failed</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 sm:p-8">
                        <!-- Route -->
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div class="flex-1">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Asal</div>
                                <div class="text-xl sm:text-2xl font-black text-gray-900">{{ booking.schedule.bus_route.origin_city.name }}</div>
                            </div>
                            <div class="shrink-0 text-gray-300 px-2">
                                <ArrowRight class="w-6 h-6" />
                            </div>
                            <div class="flex-1 text-right">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tujuan</div>
                                <div class="text-xl sm:text-2xl font-black text-gray-900">{{ booking.schedule.bus_route.destination_city.name }}</div>
                            </div>
                        </div>

                        <!-- Schedule & Bus Details -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-8">
                            <div class="inline-flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                                <Clock class="w-4 h-4 text-gray-400" />
                                <span class="text-sm font-bold text-gray-700">{{ formatTime(booking.schedule.departure_time) }}</span>
                            </div>
                            <div class="inline-flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Armada</span>
                                <span class="text-sm font-black text-gray-900 uppercase">{{ booking.schedule.bus.name }}</span>
                                <span class="text-[#d4af37] text-xs font-black">&bull; {{ booking.schedule.bus.layout_type }}</span>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-100">
                            <div class="w-full sm:w-auto flex justify-between sm:justify-start items-center gap-6">
                                <div>
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Pembayaran</div>
                                    <div class="text-xl font-black text-[#e11d48]">{{ formatPrice(booking.total_price) }}</div>
                                </div>
                            </div>
                            <div class="w-full sm:w-auto flex gap-3">
                                <Link v-if="booking.status === 'pending'" :href="`/booking/${booking.id}/payment`" class="flex-1 sm:flex-none px-8 py-3.5 rounded-xl bg-[#e11d48] text-xs font-black uppercase tracking-widest text-white hover:bg-red-700 transition-all text-center shadow-lg shadow-red-200 active:scale-95">
                                    Lanjut Bayar
                                </Link>
                                <Link v-if="booking.status === 'paid'" :href="`/booking/${booking.id}/ticket`" class="flex-1 sm:flex-none px-8 py-3.5 rounded-xl bg-gray-900 text-xs font-black uppercase tracking-widest text-white hover:bg-black transition-all text-center shadow-lg shadow-gray-200 active:scale-95">
                                    Lihat E-Ticket
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
.font-sans {
    font-family: 'Inter', sans-serif;
}
</style>

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

const props = defineProps<{ bookings: any[] }>();

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

    <div class="min-h-screen bg-[#fdfdfd] text-gray-900 font-sans">
        <!-- Navigation Header -->
        <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50 backdrop-blur-md bg-white/80">
            <nav class="flex items-center justify-between p-4 lg:px-8 max-w-7xl mx-auto" aria-label="Global">
                <div class="flex lg:flex-1">
                    <Link href="/" class="-m-1.5 p-1.5">
                        <span class="sr-only">HDEX Trans</span>
                        <div class="text-2xl font-black tracking-tighter text-gray-900">
                            HDEX<span class="text-[#d4af37]">TRANS</span>
                        </div>
                    </Link>
                </div>

                <div class="flex flex-1 justify-end items-center gap-x-4 lg:gap-x-6">
                    <template v-if="$page.props.auth?.user">
                        <template v-if="$page.props.auth.user.role === 'admin'">
                            <Link href="/dashboard" class="rounded-full bg-[#d4af37] px-5 py-2 text-sm font-bold text-white shadow hover:bg-[#b08d26] transition">Dashboard</Link>
                        </template>
                        <template v-else>
                            <div class="relative">
                                <button @click="isUserMenuOpen = !isUserMenuOpen" class="flex items-center gap-2 rounded-full bg-white border border-gray-200 pl-2 pr-4 py-1.5 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                                    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 overflow-hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 mt-1"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" /></svg>
                                    </div>
                                    {{ $page.props.auth.user.name }}
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400" :class="{'rotate-180': isUserMenuOpen}"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                </button>
                                
                                <div v-if="isUserMenuOpen" @click.away="isUserMenuOpen = false" class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 transform origin-top-right transition-all">
                                    <div class="px-4 py-3 border-b border-gray-50 mb-1">
                                        <p class="text-sm font-bold text-gray-900">{{ $page.props.auth.user.name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $page.props.auth.user.email }}</p>
                                    </div>
                                    <Link href="/settings/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-[#d4af37]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                        Profil Saya
                                    </Link>
                                    <Link href="/user/bookings" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-[#d4af37]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" /></svg>
                                        Riwayat Pesanan
                                    </Link>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <Link href="/logout" method="post" as="button" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 text-left">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                                        Keluar
                                    </Link>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            </nav>
        </header>

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

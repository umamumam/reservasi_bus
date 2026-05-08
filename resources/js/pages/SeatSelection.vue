<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    schedule: any;
    bookedSeats: string[];
    auth: any;
}>();

const page = usePage();
const isUserMenuOpen = ref(false);

const form = useForm({
    schedule_id: props.schedule.id,
    seats: [] as string[],
});

const toggleSeat = (seatNumber: string) => {
    if (props.bookedSeats.includes(seatNumber)) return;
    
    if (form.seats.includes(seatNumber)) {
        form.seats = form.seats.filter(s => s !== seatNumber);
    } else {
        form.seats.push(seatNumber);
    }
};

const isSeatBooked = (seatNumber: string) => props.bookedSeats.includes(seatNumber);
const isSeatSelected = (seatNumber: string) => form.seats.includes(seatNumber);

// Generate seats based on layout
const getSeatRows = () => {
    const total = props.schedule.bus.total_seats;
    const layout = props.schedule.bus.layout_type;
    const rows = [];
    
    if (layout === '2-2') {
        const rowCount = Math.ceil(total / 4);
        let seatIdx = 1;
        for (let i = 0; i < rowCount; i++) {
            const row = { id: i, left: [] as string[], right: [] as string[] };
            // Left side (2 seats)
            for(let j = 0; j < 2; j++) {
                if (seatIdx <= total) {
                    row.left.push(seatIdx.toString());
                    seatIdx++;
                }
            }
            // Right side (2 seats)
            for(let j = 0; j < 2; j++) {
                if (seatIdx <= total) {
                    row.right.push(seatIdx.toString());
                    seatIdx++;
                }
            }
            rows.push(row);
        }
    } else if (layout === 'sleeper') {
        const rowCount = Math.ceil(total / 2);
        let seatIdx = 1;
        for (let i = 0; i < rowCount; i++) {
            const row = { id: i, left: [] as string[], right: [] as string[] };
            if (seatIdx <= total) {
                row.left.push(seatIdx.toString() + 'A');
                seatIdx++;
            }
            if (seatIdx <= total) {
                row.right.push(seatIdx.toString() + 'B');
                seatIdx++;
            }
            rows.push(row);
        }
    }
    
    return rows;
};

const seatRows = computed(() => getSeatRows());

const totalPrice = computed(() => {
    return form.seats.length * props.schedule.price;
});

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(price);
};

const proceedToBooking = () => {
    if (form.seats.length === 0) {
        alert("Silakan pilih minimal 1 kursi.");
        return;
    }
    
    form.post('/booking', {
        onSuccess: () => {
            // Redirect will be handled by Laravel
        },
        onError: (errors) => {
            if (errors.seats) alert(errors.seats);
        }
    });
};
</script>

<template>
    <Head title="Pilih Kursi | HDEX Trans Bus" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 selection:bg-[#d4af37] selection:text-white pb-32">
        <!-- Navigation (Same as Welcome) -->
        <header class="fixed inset-x-0 top-0 z-50 border-b border-gray-200 bg-white/90 shadow-sm backdrop-blur-md">
            <nav class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8">
                <div class="flex lg:flex-1">
                    <Link href="/" class="-m-1.5 p-1.5">
                        <div class="text-2xl font-black tracking-tighter text-gray-900">
                            HDEX<span class="text-[#d4af37]">TRANS</span>
                        </div>
                    </Link>
                </div>

                <div class="flex flex-1 items-center justify-end gap-x-4 lg:gap-x-6">
                    <template v-if="auth.user">
                        <div class="relative">
                            <button @click="isUserMenuOpen = !isUserMenuOpen" class="flex items-center gap-2 rounded-full border border-gray-200 bg-white py-1.5 pr-4 pl-2 text-sm font-bold text-gray-700 shadow-sm transition hover:bg-gray-50">
                                <div class="flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-gray-100 text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mt-1 h-5 w-5"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" /></svg>
                                </div>
                                {{ auth.user.name }}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-gray-400" :class="{'rotate-180': isUserMenuOpen}"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                            </button>
                            <div v-if="isUserMenuOpen" @click.away="isUserMenuOpen = false" class="absolute right-0 z-50 mt-3 w-56 origin-top-right transform rounded-2xl border border-gray-100 bg-white py-2 shadow-xl transition-all">
                                <div class="mb-1 border-b border-gray-50 px-4 py-3">
                                    <p class="text-sm font-bold text-gray-900">{{ auth.user.name }}</p>
                                    <p class="truncate text-xs text-gray-500">{{ auth.user.email }}</p>
                                </div>
                                <Link href="/settings/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-[#d4af37]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                    Profil Saya
                                </Link>
                                <Link href="/user/bookings" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-[#d4af37]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" /></svg>
                                    Riwayat Pesanan
                                </Link>
                                <div class="my-1 border-t border-gray-100"></div>
                                <Link href="/logout" method="post" as="button" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-bold text-red-600 hover:bg-red-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                                    Keluar
                                </Link>
                            </div>
                        </div>
                    </template>
                </div>
            </nav>
        </header>

        <main class="pt-28 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Info Section -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                        <h2 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-[#e11d48]"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                            Detail Perjalanan
                        </h2>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-1.5 h-16 bg-gradient-to-b from-[#e11d48] to-gray-200 rounded-full"></div>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Keberangkatan</p>
                                        <p class="font-bold text-gray-900">{{ schedule.bus_route.origin_city.name }}</p>
                                        <p class="text-sm text-gray-500 font-medium">{{ new Date(schedule.departure_time).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Kedatangan</p>
                                        <p class="font-bold text-gray-900">{{ schedule.bus_route.destination_city.name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-gray-50">
                                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Armada</p>
                                <p class="font-black text-gray-900">{{ schedule.bus.name }}</p>
                                <p class="text-xs font-bold text-[#d4af37]">{{ schedule.bus.layout_type === 'sleeper' ? 'SLEEPER CLASS' : 'ROYAL CLASS' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                        <h2 class="text-xl font-black text-gray-900 mb-6">Keterangan</h2>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-xl bg-gray-50 border border-gray-100"></div>
                                <span class="text-sm font-bold text-gray-600">Tersedia</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-xl bg-[#e11d48]/10 border border-[#e11d48] flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full bg-[#e11d48]"></div>
                                </div>
                                <span class="text-sm font-bold text-[#e11d48]">Dipilih</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-400">Sudah Terisi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seat Layout Section -->
                <div class="lg:col-span-8 flex flex-col items-center">
                    <div class="bg-white rounded-[2.5rem] sm:rounded-[3rem] p-6 sm:p-10 w-full max-w-md shadow-xl border border-gray-100 relative">
                        <!-- Dashboard Area -->
                        <div class="flex justify-between items-center mb-12 border-b border-gray-50 pb-8">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0112 2.25c6.627 0 12 5.373 12 12s-5.373 12-12 12-12-5.373-12-12c0-3.313 1.343-6.313 3.515-8.485M12 12l7.21-7.21" /></svg>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pintu</span>
                            </div>
                            <div class="h-10 w-[2px] bg-gray-100"></div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Supir</span>
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="space-y-4 sm:space-y-6">
                            <div v-for="row in seatRows" :key="row.id" class="flex justify-between items-center px-1 sm:px-0">
                                <!-- Left side -->
                                <div class="flex gap-3 sm:gap-4">
                                    <button 
                                        v-for="seat in row.left" :key="seat"
                                        @click="toggleSeat(seat)"
                                        :disabled="isSeatBooked(seat)"
                                        class="w-12 h-14 sm:w-14 sm:h-16 rounded-2xl flex flex-col items-center justify-center font-black text-sm transition-all duration-300 relative group"
                                        :class="{
                                            'bg-gray-100 text-gray-300 cursor-not-allowed': isSeatBooked(seat),
                                            'bg-[#e11d48] text-white shadow-lg shadow-red-200 scale-110 z-10': isSeatSelected(seat),
                                            'bg-white border-2 border-gray-100 text-gray-600 hover:border-[#e11d48] hover:text-[#e11d48] shadow-sm': !isSeatBooked(seat) && !isSeatSelected(seat)
                                        }"
                                    >
                                        {{ seat }}
                                        <div v-if="isSeatBooked(seat)" class="absolute -top-1 -right-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-gray-400 bg-white rounded-full"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 12.72l5.25-5.25a.75.75 0 10-1.06-1.06L10.25 12.94l-2.22-2.22a.75.75 0 00-1.06 1.06l2.75 2.75a.75.75 0 001.06 0z" clip-rule="evenodd" /></svg>
                                        </div>
                                    </button>
                                </div>

                                <!-- Aisle -->
                                <div class="w-6 sm:w-8 h-full flex flex-col items-center justify-center shrink-0">
                                    <div class="w-[2px] h-10 sm:h-12 bg-gray-100 rounded-full"></div>
                                </div>

                                <!-- Right side -->
                                <div class="flex gap-3 sm:gap-4">
                                    <button 
                                        v-for="seat in row.right" :key="seat"
                                        @click="toggleSeat(seat)"
                                        :disabled="isSeatBooked(seat)"
                                        class="w-12 h-14 sm:w-14 sm:h-16 rounded-2xl flex flex-col items-center justify-center font-black text-sm transition-all duration-300 relative group"
                                        :class="{
                                            'bg-gray-100 text-gray-300 cursor-not-allowed': isSeatBooked(seat),
                                            'bg-[#e11d48] text-white shadow-lg shadow-red-200 scale-110 z-10': isSeatSelected(seat),
                                            'bg-white border-2 border-gray-100 text-gray-600 hover:border-[#e11d48] hover:text-[#e11d48] shadow-sm': !isSeatBooked(seat) && !isSeatSelected(seat)
                                        }"
                                    >
                                        {{ seat }}
                                        <div v-if="isSeatBooked(seat)" class="absolute -top-1 -right-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-gray-400 bg-white rounded-full"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 12.72l5.25-5.25a.75.75 0 10-1.06-1.06L10.25 12.94l-2.22-2.22a.75.75 0 00-1.06 1.06l2.75 2.75a.75.75 0 001.06 0z" clip-rule="evenodd" /></svg>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Rear Bumper Decoration -->
                        <div class="mt-12 h-4 w-full bg-gray-50 rounded-b-3xl"></div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Sticky Bottom Action -->
        <div class="fixed bottom-0 inset-x-0 bg-white/80 backdrop-blur-xl border-t border-gray-100 p-6 z-50">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-8">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Total Kursi</p>
                        <p class="text-xl font-black text-gray-900">{{ form.seats.length }} <span class="text-sm font-bold text-gray-500">dipilih</span></p>
                    </div>
                    <div class="h-10 w-[1px] bg-gray-100"></div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Total Bayar</p>
                        <p class="text-2xl font-black text-[#e11d48]">{{ formatPrice(totalPrice) }}</p>
                    </div>
                </div>
                <button 
                    @click="proceedToBooking" 
                    :disabled="form.seats.length === 0 || form.processing" 
                    class="w-full sm:w-auto flex items-center justify-center gap-3 rounded-2xl bg-[#e11d48] px-10 py-4 text-sm font-black uppercase tracking-widest text-white shadow-xl shadow-red-200 hover:bg-red-700 transition active:scale-95 disabled:opacity-50"
                >
                    {{ form.processing ? 'Memproses...' : 'Lanjut Pembayaran' }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>
            </div>
        </div>
    </div>
</template>


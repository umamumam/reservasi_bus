<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    booking: any;
    auth: any;
}>();

const isUserMenuOpen = ref(false);
const selectedMethod = ref('va');

const form = useForm({});

const submitPayment = () => {
    form.post(`/booking/${props.booking.id}/pay`);
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(price);
};

// Assuming VA number might come from backend, using a fallback for preview
const vaNumber = computed(() => props.booking.va_number || '8802 0895 1234 5678');

</script>

<template>
    <Head title="Pembayaran | HDEX Trans" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 pb-20 relative overflow-x-hidden">
        <!-- Navigation -->
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
                </div>
            </nav>
        </header>

        <main class="pt-32 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Pembayaran Tiket</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="px-3 py-1 rounded-full bg-[#d4af37]/10 text-[#d4af37] text-[10px] font-black uppercase tracking-widest">Order #{{ booking.booking_code }}</span>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <!-- Left: Payment Methods -->
                <div class="w-full lg:w-7/12 space-y-6">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <h2 class="text-xl font-black text-gray-900 mb-6">Pilih Metode Pembayaran</h2>
                        <div class="grid grid-cols-1 gap-4">
                            <label class="relative flex items-center gap-4 p-5 rounded-2xl border-2 transition cursor-pointer" :class="selectedMethod === 'va' ? 'border-[#e11d48] bg-red-50/50' : 'border-gray-50 hover:border-gray-200'">
                                <input type="radio" v-model="selectedMethod" value="va" class="hidden">
                                <div class="w-14 h-14 rounded-xl bg-white border border-gray-100 flex items-center justify-center font-black text-[#e11d48] text-sm">VA</div>
                                <div class="flex-1">
                                    <p class="font-black text-gray-900 text-base">Virtual Account</p>
                                    <p class="text-sm text-gray-500 font-bold">BCA, Mandiri, BNI, BRI</p>
                                </div>
                            </label>

                            <label class="relative flex items-center gap-4 p-5 rounded-2xl border-2 transition cursor-pointer" :class="selectedMethod === 'qris' ? 'border-[#e11d48] bg-red-50/50' : 'border-gray-50 hover:border-gray-200'">
                                <input type="radio" v-model="selectedMethod" value="qris" class="hidden">
                                <div class="w-14 h-14 rounded-xl bg-white border border-gray-100 flex items-center justify-center font-black text-[#e11d48] text-sm italic">QRIS</div>
                                <div class="flex-1">
                                    <p class="font-black text-gray-900 text-base">QRIS / E-Wallet</p>
                                    <p class="text-sm text-gray-500 font-bold">Gopay, OVO, Dana</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div v-if="selectedMethod === 'va'" class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <h2 class="text-lg font-black text-gray-900 mb-6">Informasi Pembayaran</h2>
                        <div class="rounded-2xl bg-gray-50 p-5 sm:p-6 border border-gray-100 mb-6">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nomor Virtual Account</p>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                                <p class="font-black text-gray-900 tracking-wider break-all"
                                   :class="vaNumber.length > 18 ? 'text-lg sm:text-xl' : 'text-2xl sm:text-3xl'">
                                    {{ vaNumber }}
                                </p>
                                <button class="shrink-0 rounded-lg bg-red-50 px-4 py-2 text-[#e11d48] font-black text-xs uppercase tracking-widest transition hover:bg-red-100 active:scale-95">Salin</button>
                            </div>
                        </div>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm font-bold text-gray-500">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#d4af37] mt-2"></div>
                                <p>Pilih menu Transfer Virtual Account di m-banking Anda.</p>
                            </li>
                            <li class="flex items-start gap-3 text-sm font-bold text-gray-500">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#d4af37] mt-2"></div>
                                <p>Verifikasi otomatis setelah transaksi sukses.</p>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right: Summary -->
                <div class="w-full lg:w-5/12">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 sticky top-32">
                        <h2 class="text-xl font-black text-gray-900 mb-6">Ringkasan Pesanan</h2>
                        
                        <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100 mb-6">
                            <p class="text-sm font-black text-gray-900">{{ booking.schedule.bus_route.origin_city.name }} &rarr; {{ booking.schedule.bus_route.destination_city.name }}</p>
                            <p class="text-xs font-bold text-gray-400 mt-1">{{ new Date(booking.schedule.departure_time).toLocaleDateString('id-ID', {day: '2-digit', month: 'short'}) }} &bull; {{ booking.schedule.bus.name }}</p>
                            <div class="flex flex-wrap gap-2 mt-4">
                                <span v-for="seat in booking.seats" :key="seat.id" class="px-3 py-1 rounded-lg bg-white text-[10px] font-black text-[#d4af37] border border-[#d4af37]/20">K-{{ seat.seat_number }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-3 pt-6 border-t border-gray-100">
                            <div class="flex justify-between items-center text-sm font-bold">
                                <span class="text-gray-400">Total Tiket</span>
                                <span class="text-gray-900">{{ formatPrice(booking.total_price) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-3">
                                <span class="text-sm font-black text-gray-900">Total Bayar</span>
                                <span class="text-2xl font-black text-[#e11d48]">{{ formatPrice(booking.total_price) }}</span>
                            </div>
                        </div>

                        <button @click="submitPayment" :disabled="form.processing" class="w-full mt-8 rounded-2xl bg-[#e11d48] py-4 text-sm font-black uppercase tracking-widest text-white shadow-xl shadow-red-200 hover:bg-red-700 transition active:scale-95 disabled:opacity-50">
                            {{ form.processing ? 'Memproses...' : 'Konfirmasi Bayar' }}
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>


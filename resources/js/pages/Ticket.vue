<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import QrcodeVue from 'qrcode.vue';
import html2canvas from 'html2canvas';

const props = defineProps<{
    booking: any;
}>();

const ticketRef = ref<HTMLElement | null>(null);

const printTicket = () => {
    window.print();
};

const downloadTicket = async () => {
    if (!ticketRef.value) return;
    
    // Store original styles to restore them after screenshot
    const originalBorderRadius = ticketRef.value.style.borderRadius;
    const originalBoxShadow = ticketRef.value.style.boxShadow;
    
    // Temporarily remove rounded corners and shadow for a cleaner image
    ticketRef.value.style.borderRadius = '0px';
    ticketRef.value.style.boxShadow = 'none';

    try {
        // Wait a bit for any pending renders
        await new Promise(resolve => setTimeout(resolve, 100));

        const canvas = await html2canvas(ticketRef.value, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false,
            width: ticketRef.value.offsetWidth,
            height: ticketRef.value.offsetHeight
        });
        
        const link = document.createElement('a');
        link.download = `Tiket-HDEX-${props.booking.booking_code}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    } catch (error) {
        console.error('Error generating image:', error);
        alert('Gagal mengunduh tiket. Silakan coba lagi.');
    } finally {
        // Restore styles
        ticketRef.value.style.borderRadius = originalBorderRadius;
        ticketRef.value.style.boxShadow = originalBoxShadow;
    }
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(price);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

const formatTime = (date: string) => {
    return new Date(date).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="E-Ticket | HDEX Trans" />

    <div class="min-h-screen bg-gray-100 p-3 sm:p-8 font-sans print:bg-white print:p-0">
        <!-- Back Button (Hidden on Print) -->
        <div class="max-w-4xl mx-auto mb-6 flex flex-col sm:flex-row gap-4 justify-between items-center print:hidden">
            <Link :href="$page.props.auth?.user?.role === 'admin' ? '/admin/bookings' : '/user/bookings'" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#e11d48] transition self-start sm:self-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                Kembali ke {{ $page.props.auth?.user?.role === 'admin' ? 'Daftar Transaksi' : 'Riwayat' }}
            </Link>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a :href="`/booking/${booking.id}/pdf`" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-gray-900 text-white text-xs font-black uppercase tracking-widest hover:bg-black transition active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    Unduh PDF
                </a>
                <button @click="downloadTicket" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-white border-2 border-gray-200 text-gray-900 text-xs font-black uppercase tracking-widest hover:border-gray-300 hover:bg-gray-50 transition active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Unduh Gambar
                </button>
                <button @click="printTicket" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-[#e11d48] text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-red-200 hover:bg-red-700 transition active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231a1.125 1.125 0 01-1.12-1.227L6.34 18m11.318-4.171a3.125 3.125 0 00-3.076-3.117H9.418a3.125 3.125 0 00-3.076 3.117m11.318 0h1.125a1.125 1.125 0 001.125-1.125V9.75a1.125 1.125 0 00-1.125-1.125h-15a1.125 1.125 0 00-1.125 1.125v3.125a1.125 1.125 0 001.125 1.125h1.125m10.125-11.25V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.375" /></svg>
                    Cetak Tiket
                </button>
            </div>
        </div>

        <div ref="ticketRef" class="max-w-4xl mx-auto bg-white rounded-[2rem] shadow-2xl overflow-hidden print:shadow-none print:rounded-none flex flex-col lg:flex-row relative">
            <!-- Left Side: Main Info -->
            <div class="flex-1 p-6 sm:p-10 border-b-2 lg:border-b-0 lg:border-r-2 border-dashed border-gray-200 relative">
                <!-- Watermark Logo -->
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
                    <span class="text-[6rem] sm:text-[12rem] font-black rotate-[-35deg]">HDEX</span>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-10 relative z-10">
                    <div class="text-2xl sm:text-3xl font-black tracking-tighter text-gray-900">
                        HDEX<span class="text-[#d4af37]">TRANS</span>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kode Booking</p>
                        <p class="text-lg sm:text-xl font-black text-[#e11d48] tracking-widest">#{{ booking.booking_code }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-8 sm:gap-y-10 relative z-10">
                    <div class="col-span-2">
                        <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color: #9ca3af;">Penumpang</p>
                        <p class="text-xl sm:text-2xl font-black leading-none" style="color: #111827;">{{ booking.user.name }}</p>
                    </div>

                    <div class="col-span-1">
                        <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color: #9ca3af;">Asal</p>
                        <p class="text-lg sm:text-xl font-black leading-tight" style="color: #111827;">{{ booking.schedule.bus_route.origin_city.name }}</p>
                        <p class="text-[10px] font-bold mt-1" style="color: #6b7280;">{{ formatDate(booking.schedule.departure_time).split(',')[0] }}, {{ formatDate(booking.schedule.departure_time).split(',')[1] }}</p>
                        <p class="text-lg sm:text-xl font-black mt-1" style="color: #e11d48;">{{ formatTime(booking.schedule.departure_time) }}</p>
                    </div>

                    <div class="col-span-1">
                        <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color: #9ca3af;">Tujuan</p>
                        <p class="text-lg sm:text-xl font-black leading-tight" style="color: #111827;">{{ booking.schedule.bus_route.destination_city.name }}</p>
                        <p class="text-[10px] font-bold mt-1" style="color: #6b7280;">{{ formatDate(booking.schedule.arrival_time).split(',')[0] }}, {{ formatDate(booking.schedule.arrival_time).split(',')[1] }}</p>
                        <p class="text-lg sm:text-xl font-black mt-1" style="color: #374151;">~{{ formatTime(booking.schedule.arrival_time) }}</p>
                    </div>

                    <div class="col-span-1">
                        <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color: #9ca3af;">Bus & Kelas</p>
                        <p class="text-xs sm:text-sm font-black uppercase leading-tight" style="color: #111827;">{{ booking.schedule.bus.name }}</p>
                        <p class="text-[10px] font-bold mt-0.5" style="color: #6b7280;">{{ booking.schedule.bus.layout_type === '2-2' ? 'Executive 2-2' : 'Sleeper Class' }}</p>
                    </div>

                    <div class="col-span-1">
                        <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color: #9ca3af;">Nomor Kursi</p>
                        <div class="flex flex-wrap gap-1.5">
                            <span v-for="seat in booking.seats" :key="seat.id" class="px-2.5 py-1 rounded-lg text-white text-[10px] font-black" style="background-color: #111827;">
                                {{ seat.seat_number }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Perforation Circles -->
                <div class="hidden lg:block absolute -top-4 -right-4 w-8 h-8 bg-gray-100 rounded-full print:hidden"></div>
                <div class="hidden lg:block absolute -bottom-4 -right-4 w-8 h-8 bg-gray-100 rounded-full print:hidden"></div>
            </div>

            <!-- Right Side: Barcode & Footer -->
            <div class="lg:w-72 p-8 sm:p-10 flex flex-col items-center justify-between gap-10" style="background-color: #f9fafb;">
                <div class="text-center w-full">
                    <p class="text-[10px] font-black uppercase tracking-widest mb-6" style="color: #9ca3af;">QR Code Tiket</p>
                    <div class="w-full bg-white shadow-sm rounded-2xl flex flex-col items-center justify-center p-6 gap-4" style="border: 1px solid #f3f4f6;">
                        <QrcodeVue :value="booking.booking_code" :size="120" level="H" render-as="canvas" />
                        <p class="font-mono text-[10px] sm:text-[11px] font-black tracking-[0.3em] uppercase" style="color: #111827;">{{ booking.booking_code }}</p>
                    </div>
                    <p class="mt-6 text-[9px] font-bold leading-relaxed uppercase tracking-tighter" style="color: #9ca3af;">
                        Tunjukkan QR Code ini kepada petugas di terminal keberangkatan.
                    </p>
                </div>

                <div class="w-full pt-8 text-center" style="border-top: 1px solid #e5e7eb;">
                    <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color: #9ca3af;">Status</p>
                    <div class="inline-flex px-5 py-2 rounded-full text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-green-200" style="background-color: #22c55e;">
                        LUNAS
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms (Print Only) -->
        <div class="max-w-4xl mx-auto mt-10 hidden print:block text-[10px] text-gray-400 space-y-4 px-10">
            <h3 class="font-black text-gray-600 uppercase tracking-widest">Syarat & Ketentuan:</h3>
            <ul class="list-disc pl-4 space-y-1 font-bold">
                <li>Tiket ini adalah bukti pembayaran yang sah untuk perjalanan sesuai rute yang tertera.</li>
                <li>Harap tiba di titik keberangkatan minimal 30 menit sebelum jadwal keberangkatan.</li>
                <li>Petugas berhak menolak penumpang yang tidak memiliki bukti tiket digital/cetak yang sah.</li>
                <li> HDEX Trans tidak bertanggung jawab atas keterlambatan akibat kondisi lalu lintas atau force majeure.</li>
            </ul>
        </div>
    </div>
</template>

<style>
@media print {
    body {
        -webkit-print-color-adjust: exact;
    }
}
</style>

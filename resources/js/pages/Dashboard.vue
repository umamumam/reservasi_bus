<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { 
    Bus, 
    Route, 
    Calendar, 
    Ticket, 
    Wallet, 
    TrendingUp, 
    Users, 
    Activity, 
    ArrowUpRight,
    MapPin,
    Clock,
    LayoutGrid,
    ChevronRight,
    Bell,
    Download
} from 'lucide-vue-next';
import { computed, onMounted } from 'vue';
import Swal from 'sweetalert2';
import { 
    Chart as ChartJS, 
    CategoryScale, 
    LinearScale, 
    PointElement, 
    LineElement, 
    Title, 
    Tooltip, 
    Legend, 
    Filler,
    type ChartOptions
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(
    CategoryScale, 
    LinearScale, 
    PointElement, 
    LineElement, 
    Title, 
    Tooltip, 
    Legend, 
    Filler
);

const props = defineProps<{
    stats?: {
        total_buses: number;
        total_routes: number;
        total_schedules: number;
        total_bookings: number;
        revenue: number;
    };
    recentBookings?: any[];
    chartData?: Array<{date: string, amount: number}>;
}>();

const page = usePage<any>();
const isAdmin = page.props.auth?.user?.role === 'admin';

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(price);
};

const formatTime = (timeStr: string) => {
    return new Date(timeStr).toLocaleString('id-ID', { 
        day: '2-digit', 
        month: 'short', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
};

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 17) return 'Selamat Siang';
    return 'Selamat Malam';
});

// Chart Configuration
const chartConfig = computed(() => {
    const labels = props.chartData?.map(d => d.date) || [];
    const data = props.chartData?.map(d => d.amount) || [];

    return {
        labels,
        datasets: [
            {
                label: 'Pendapatan (IDR)',
                backgroundColor: 'rgba(212, 175, 55, 0.1)',
                borderColor: '#d4af37',
                pointBackgroundColor: '#d4af37',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#d4af37',
                fill: true,
                tension: 0.4,
                data: data
            }
        ]
    };
});

const chartOptions: ChartOptions<'line'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: '#000',
            titleFont: { size: 10, weight: 'bold' },
            bodyFont: { size: 12, weight: 'bold' },
            padding: 12,
            displayColors: false,
            callbacks: {
                label: (context: any) => {
                    return `IDR ${context.parsed.y.toLocaleString('id-ID')}`;
                }
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks: {
                font: { size: 10, weight: 'bold' },
                color: '#9ca3af'
            }
        },
        y: {
            grid: {
                color: 'rgba(156, 163, 175, 0.1)'
            },
            ticks: {
                font: { size: 10, weight: 'bold' },
                color: '#9ca3af',
                callback: (value: any) => {
                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                    if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                    return value;
                }
            }
        }
    }
};

const handleExport = () => {
    Swal.fire({
        title: 'Export Data?',
        text: "Seluruh data analitik akan diunduh dalam format Excel.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#f3f4f6',
        confirmButtonText: 'Ya, Export!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-[2rem] border-none shadow-2xl',
            confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs',
            cancelButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs text-gray-400'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data sedang diproses untuk diunduh.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-[2rem] border-none shadow-2xl'
                }
            });
        }
    });
};

const showAlert = (title: string, message: string) => {
    Swal.fire({
        title,
        text: message,
        icon: 'info',
        confirmButtonColor: '#000',
        customClass: {
            popup: 'rounded-[2rem] border-none shadow-2xl',
            confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs'
        }
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Visionary Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-8 p-4 sm:p-8 bg-[#fdfdfd] dark:bg-[#050505]">
        
        <!-- Ultra-Premium Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div data-aos="fade-right">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded bg-[#d4af37]/10 text-[#d4af37] text-[10px] font-black uppercase tracking-[0.2em]">Operational Command</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/20"></span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' }) }}</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                    {{ greeting }}, {{ page.props.auth?.user?.name.split(' ')[0] }}
                    <span class="animate-pulse w-2 h-2 rounded-full bg-[#d4af37]"></span>
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">
                    {{ isAdmin ? 'Sistem pusat kendali armada dan analitik HDEX Trans.' : 'Kelola perjalanan eksklusif Anda dalam satu pusat kendali.' }}
                </p>
            </div>
            
            <div class="flex items-center gap-3" data-aos="fade-left">
                <button @click="showAlert('Notifikasi', 'Tidak ada notifikasi baru saat ini.')" class="relative p-3 rounded-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-md transition group">
                    <Bell class="w-5 h-5 text-gray-400 group-hover:text-[#d4af37] transition" />
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-[#e11d48] border-2 border-white dark:border-[#050505]"></span>
                </button>
                <div class="h-10 w-[1px] bg-gray-200 dark:bg-white/10 mx-2"></div>
                <div class="flex items-center gap-4 px-4 py-2 rounded-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 shadow-sm">
                    <Activity class="w-4 h-4 text-green-500 animate-spin-slow" />
                    <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">System Online</span>
                </div>
            </div>
        </header>

        <!-- Admin: Advanced Analytic Modules -->
        <template v-if="isAdmin && stats">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                <!-- Module: Revenue (The Spotlight) -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-2 xl:col-span-2 rounded-[2.5rem] bg-gradient-to-br from-gray-900 via-gray-900 to-black p-8 text-white relative overflow-hidden shadow-2xl group" data-aos="zoom-in">
                    <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-110 transition duration-1000">
                        <Wallet class="w-64 h-64 text-[#d4af37]" />
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-8">
                            <div class="p-2 rounded-lg bg-white/10 backdrop-blur-md">
                                <TrendingUp class="w-4 h-4 text-[#d4af37]" />
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Total Pendapatan</span>
                        </div>
                        <h3 class="text-5xl font-black tracking-tighter mb-2 italic">
                            {{ formatPrice(stats.revenue).replace('Rp', '') }}
                            <span class="text-lg font-bold text-[#d4af37] not-italic ml-[-5px]">IDR</span>
                        </h3>
                        <div class="flex items-center gap-2 text-green-400 text-sm font-bold">
                            <ArrowUpRight class="w-4 h-4" />
                            <span>+12.5% dibanding bulan lalu</span>
                        </div>
                    </div>
                </div>

                <!-- Module: Capacity -->
                <div class="rounded-[2.5rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 p-8 shadow-sm hover:shadow-xl transition group relative overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <div class="absolute right-6 top-6">
                        <Bus class="w-6 h-6 text-gray-200 dark:text-white/10 group-hover:text-[#d4af37] transition duration-500" />
                    </div>
                    <div class="flex flex-col h-full justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-4 block">Kapasitas Armada</span>
                            <div class="text-4xl font-black text-gray-900 dark:text-white mb-2">{{ stats.total_buses }}</div>
                            <div class="text-xs font-bold text-gray-400">Bus Terdaftar</div>
                        </div>
                        <div class="w-full h-1 bg-gray-100 dark:bg-white/10 rounded-full mt-6 overflow-hidden">
                            <div class="w-3/4 h-full bg-[#d4af37] rounded-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Module: Operations -->
                <div class="rounded-[2.5rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 p-8 shadow-sm hover:shadow-xl transition group relative overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <div class="absolute right-6 top-6">
                        <Route class="w-6 h-6 text-gray-200 dark:text-white/10 group-hover:text-blue-500 transition duration-500" />
                    </div>
                    <div class="flex flex-col h-full justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-4 block">Cakupan Rute</span>
                            <div class="text-4xl font-black text-gray-900 dark:text-white mb-2">{{ stats.total_routes }}</div>
                            <div class="text-xs font-bold text-gray-400">Koneksi Aktif</div>
                        </div>
                        <div class="flex -space-x-2 mt-6">
                            <div class="w-6 h-6 rounded-full border-2 border-white dark:border-[#050505] bg-gray-200"></div>
                            <div class="w-6 h-6 rounded-full border-2 border-white dark:border-[#050505] bg-gray-300"></div>
                            <div class="w-6 h-6 rounded-full border-2 border-white dark:border-[#050505] bg-gray-400"></div>
                        </div>
                    </div>
                </div>

                <!-- Module: Engagement -->
                <div class="rounded-[2.5rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 p-8 shadow-sm hover:shadow-xl transition group relative overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                    <div class="absolute right-6 top-6">
                        <Users class="w-6 h-6 text-gray-200 dark:text-white/10 group-hover:text-purple-500 transition duration-500" />
                    </div>
                    <div class="flex flex-col h-full justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-4 block">Okupansi Tiket</span>
                            <div class="text-4xl font-black text-gray-900 dark:text-white mb-2">{{ stats.total_bookings }}</div>
                            <div class="text-xs font-bold text-gray-400">Transaksi Berhasil</div>
                        </div>
                        <div class="text-xs font-black text-purple-500 uppercase tracking-widest mt-6">Stable Growth</div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Charts & Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">
                <!-- Revenue Chart Module -->
                <div class="lg:col-span-2 rounded-[2.5rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 p-8 shadow-sm" data-aos="fade-up">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-widest">Revenue Analytics</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Daily income trajectory - Last 7 Days</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live Feed
                            </div>
                        </div>
                    </div>
                    
                    <div class="h-[300px] w-full">
                        <Line :data="chartConfig" :options="chartOptions" />
                    </div>
                </div>

                <!-- Quick Actions & Secondary Stats -->
                <div class="space-y-8">
                    <!-- Navigation Module -->
                    <div class="rounded-[2.5rem] bg-[#d4af37]/5 border border-[#d4af37]/20 p-8 flex flex-col items-center justify-center text-center group shadow-inner" data-aos="fade-left">
                        <div class="w-20 h-20 rounded-full bg-[#d4af37] flex items-center justify-center text-white mb-6 shadow-xl shadow-[#d4af37]/30 group-hover:scale-110 transition duration-500">
                            <LayoutGrid class="w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight">Pusat Kendali</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-8">Gunakan panel navigasi kiri untuk mengakses seluruh fitur manajemen armada.</p>
                        <div class="w-full space-y-3">
                            <button @click="handleExport" class="flex items-center justify-between w-full px-6 py-4 rounded-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 text-sm font-bold text-gray-900 dark:text-white hover:border-[#d4af37] transition group/btn">
                                Export Reports <Download class="w-4 h-4 text-[#d4af37] group-hover/btn:scale-110 transition" />
                            </button>
                        </div>
                    </div>

                    <!-- Small Stat Card -->
                    <div class="rounded-[2.5rem] bg-gray-900 p-8 text-white relative overflow-hidden shadow-xl" data-aos="fade-up">
                        <div class="relative z-10">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2 block">Jadwal Aktif</span>
                            <div class="text-3xl font-black italic">{{ stats.total_schedules }} <span class="text-sm not-italic font-bold text-[#d4af37]">ROTATIONS</span></div>
                            <p class="text-[10px] text-gray-500 font-bold mt-4 uppercase tracking-[0.2em]">Operational Pulse: Optimized</p>
                        </div>
                        <Activity class="absolute -right-4 -bottom-4 w-24 h-24 text-white/5" />
                    </div>
                </div>
            </div>

            <!-- Recent Activity List -->
            <div class="rounded-[2.5rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 p-8 shadow-sm mt-4" data-aos="fade-up">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-widest">Transaksi Terbaru</h3>
                    <Link href="/admin/bookings" class="text-[10px] font-black text-[#d4af37] hover:underline uppercase tracking-[0.2em]">View All Data</Link>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="booking in recentBookings?.slice(0, 6)" :key="booking.id" 
                        @click="showAlert(booking.booking_code, `Detail transaksi untuk ${booking.user.name}. Status: ${booking.status.toUpperCase()}`)"
                        class="flex items-center justify-between p-4 rounded-3xl hover:bg-gray-50 dark:hover:bg-white/5 transition group cursor-pointer border border-transparent hover:border-gray-100 dark:hover:border-white/10">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/10 flex items-center justify-center font-black font-mono text-xs group-hover:bg-[#d4af37] group-hover:text-white transition">
                                {{ booking.booking_code.slice(-2) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ booking.user.name }}</div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ booking.schedule.bus_route.origin_city.name }} &rarr; {{ booking.schedule.bus_route.destination_city.name }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-black text-gray-900 dark:text-white">{{ formatPrice(booking.total_price) }}</div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ formatTime(booking.created_at) }}</div>
                        </div>
                    </div>
                    <div v-if="!recentBookings?.length" class="col-span-full text-center py-20 text-gray-400 italic font-medium">Belum ada aktivitas hari ini.</div>
                </div>
            </div>
        </template>

        <!-- User/Passenger: Personalized Experience Dashboard -->
        <template v-else-if="recentBookings">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content: Ticket Inventory -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest">Boarding Pass Anda</h2>
                        <Link href="/" class="text-[10px] font-black text-[#d4af37] hover:underline uppercase tracking-[0.2em]">Pesan Baru</Link>
                    </div>

                    <div v-if="recentBookings.length === 0" class="rounded-[3rem] border-2 border-dashed border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 p-20 text-center" data-aos="zoom-in">
                        <Ticket class="w-16 h-16 mx-auto text-gray-300 mb-6" />
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4">Siap untuk Berangkat?</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">Anda belum memiliki tiket aktif. Temukan rute eksklusif kami dan mulailah perjalanan Anda.</p>
                        <Link href="/" class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-[#d4af37] text-sm font-black text-white hover:bg-[#b08d26] transition shadow-xl shadow-[#d4af37]/30 group">
                            Explore Destinasi <ArrowUpRight class="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition" />
                        </Link>
                    </div>

                    <div v-else class="space-y-6">
                        <div v-for="booking in recentBookings" :key="booking.id" class="rounded-[2.5rem] border border-gray-100 dark:border-white/10 bg-white dark:bg-white/5 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 group" data-aos="fade-up">
                            <div class="px-8 py-4 bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center">
                                <div class="flex items-center gap-4">
                                    <div class="px-3 py-1 rounded bg-black text-[#d4af37] font-mono font-black text-xs tracking-widest uppercase">{{ booking.booking_code }}</div>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ formatTime(booking.created_at) }}</span>
                                </div>
                                <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em]"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': booking.status === 'paid',
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': booking.status === 'pending',
                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': booking.status === 'cancelled',
                                    }">
                                    {{ booking.status === 'paid' ? 'CONFIRMED' : booking.status }}
                                </span>
                            </div>
                            <div class="p-8">
                                <div class="flex flex-col md:flex-row items-center gap-10">
                                    <div class="flex-1 text-center md:text-left">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Origin</div>
                                        <div class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ booking.schedule.bus_route.origin_city.name }}</div>
                                        <div class="flex items-center gap-2 justify-center md:justify-start mt-2 text-sm font-bold text-[#d4af37]">
                                            <Clock class="w-4 h-4" /> {{ new Date(booking.schedule.departure_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center justify-center gap-2 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-[#d4af37]"></div>
                                            <div class="w-20 sm:w-40 h-[2px] bg-gradient-to-r from-[#d4af37] to-gray-200 dark:to-white/10 relative">
                                                <Bus class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-6 h-6 text-gray-300 dark:text-white/20 p-1 bg-white dark:bg-[#050505] rounded-full border border-gray-100 dark:border-white/10" />
                                            </div>
                                            <div class="w-2 h-2 rounded-full border-2 border-gray-200 dark:border-white/20"></div>
                                        </div>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sleeper Class</span>
                                    </div>

                                    <div class="flex-1 text-center md:text-right">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Destination</div>
                                        <div class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ booking.schedule.bus_route.destination_city.name }}</div>
                                        <div class="flex items-center gap-2 justify-center md:justify-end mt-2 text-sm font-bold text-gray-500">
                                            {{ new Date(booking.schedule.arrival_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-10 pt-8 border-t border-gray-50 dark:border-white/5 flex flex-col sm:flex-row justify-between items-center gap-6">
                                    <div class="flex items-center gap-6">
                                        <div class="flex items-center gap-2">
                                            <MapPin class="w-4 h-4 text-gray-400" />
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ booking.schedule.bus.name }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <LayoutGrid class="w-4 h-4 text-[#d4af37]" />
                                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">SEAT {{ booking.seats.map(s => s.seat_number).join(', ') }}</span>
                                        </div>
                                    </div>
                                    <Link :href="booking.status === 'paid' ? `/booking/${booking.id}/ticket` : `/booking/${booking.id}/payment`" 
                                        class="w-full sm:w-auto px-8 py-3 rounded-2xl bg-gray-900 text-white dark:bg-white dark:text-black text-[10px] font-black uppercase tracking-[0.2em] hover:scale-105 transition active:scale-95 shadow-xl">
                                        {{ booking.status === 'paid' ? 'View Boarding Pass' : 'Complete Payment' }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile & Membership Module -->
                <div class="space-y-8">
                    <div class="rounded-[3rem] bg-gradient-to-br from-[#d4af37] to-[#b08d26] p-8 text-white shadow-2xl relative overflow-hidden group" data-aos="fade-left">
                        <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition duration-700">
                            <Wallet class="w-32 h-32" />
                        </div>
                        <div class="relative z-10 text-center py-6">
                            <div class="w-20 h-20 rounded-full border-4 border-white/30 mx-auto mb-4 overflow-hidden shadow-lg">
                                <div class="w-full h-full bg-white/20 backdrop-blur-md flex items-center justify-center font-black text-2xl">
                                    {{ page.props.auth?.user?.name.charAt(0) }}
                                </div>
                            </div>
                            <h3 class="text-xl font-black uppercase tracking-tight mb-1">{{ page.props.auth?.user?.name }}</h3>
                            <div class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-black uppercase tracking-[0.2em] inline-block mb-8">Royal Member</div>
                            
                            <div class="grid grid-cols-2 gap-4 border-t border-white/20 pt-8">
                                <div>
                                    <div class="text-[10px] font-bold text-white/60 uppercase tracking-widest mb-1">Total Trip</div>
                                    <div class="text-2xl font-black italic">{{ recentBookings.length }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-white/60 uppercase tracking-widest mb-1">Point</div>
                                    <div class="text-2xl font-black italic">1,250</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[3rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 p-8 shadow-sm" data-aos="fade-up">
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-6">Informasi Penting</h3>
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-500 shrink-0">
                                    <Activity class="w-4 h-4" />
                                </div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed">Pastikan Anda berada di titik keberangkatan 30 menit sebelum jadwal.</p>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-500 shrink-0">
                                    <Bell class="w-4 h-4" />
                                </div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed">Tiket elektronik Anda adalah bukti sah perjalanan, tidak perlu dicetak.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        
    </div>
</template>

<style scoped>
.animate-spin-slow {
    animation: spin 3s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

italic {
    font-style: italic;
}
</style>

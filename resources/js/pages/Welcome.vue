<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AOS from 'aos';
import 'aos/dist/aos.css';

const props = defineProps<{
    canRegister?: boolean;
    cities: Array<{ id: number; name: string }>;
    busRoutes: Array<{ origin_city_id: number; destination_city_id: number }>;
    auth: any;
}>();

const form = useForm({
    origin: '',
    destination: '',
    date: new Date().toISOString().split('T')[0],
});

const validDestinations = computed(() => {
    if (!form.origin) return props.cities;

    const validDestIds = props.busRoutes
        .filter((r) => r.origin_city_id === form.origin)
        .map((r) => r.destination_city_id);

    return props.cities.filter((c) => validDestIds.includes(c.id));
});

const validOrigins = computed(() => {
    const validOriginIds = [
        ...new Set(props.busRoutes.map((r) => r.origin_city_id)),
    ];
    return props.cities.filter((c) => validOriginIds.includes(c.id));
});

const schedules = ref([]);
const isSearching = ref(false);
const hasSearched = ref(false);

const searchTickets = async () => {
    isSearching.value = true;
    try {
        const response = await axios.post('/api/search', form.data());
        schedules.value = response.data;
        hasSearched.value = true;
        setTimeout(() => {
            document
                .getElementById('search-results')
                ?.scrollIntoView({ behavior: 'smooth' });
        }, 100);
    } catch (e) {
        console.error(e);
    } finally {
        isSearching.value = false;
    }
};

const formatPrice = (price: string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(price));
};

const formatTime = (timeStr: string) => {
    const date = new Date(timeStr);
    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

const expandedDetail = ref<number | null>(null);
const toggleDetail = (id: number) => {
    expandedDetail.value = expandedDetail.value === id ? null : id;
};

const isMenuOpen = ref(false);
const isUserMenuOpen = ref(false);

// Background Image Slideshow
const bgImages = [
    'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=1920&auto=format&fit=crop', // Bus
    'https://images.unsplash.com/photo-1464219222984-216ebffaaf85?q=80&w=1920&auto=format&fit=crop', // Night Highway
    'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?q=80&w=1920&auto=format&fit=crop', // Coach bus
];
const currentBgIndex = ref(0);

let bgInterval: ReturnType<typeof setInterval>;

onUnmounted(() => {
    clearInterval(bgInterval);
});

// FAQ Logic
const faqs = [
    {
        q: 'Bagaimana cara menukarkan tiket elektronik?',
        a: 'Cukup tunjukkan QR Code pada tiket elektronik Anda ke kru kami atau supir saat naik (boarding). Anda tidak perlu mencetak tiket fisik.',
    },
    {
        q: 'Apakah anak kecil wajib membeli tiket penuh?',
        a: 'Anak di bawah usia 3 tahun yang dipangku tidak dikenakan biaya. Anak yang membutuhkan kursi sendiri dikenakan tarif normal.',
    },
    {
        q: 'Apakah ada layanan makan gratis?',
        a: 'Ya, khusus untuk penumpang rute jarak jauh (di atas 400 KM), kami menyediakan servis makan satu kali di restoran mitra kami secara gratis.',
    },
    {
        q: 'Bagaimana kebijakan pembatalan dan refund tiket?',
        a: 'Pembatalan dapat dilakukan maksimal 24 jam sebelum keberangkatan dengan potongan 25%. Di bawah 24 jam, tiket tidak dapat dibatalkan atau di-refund.',
    },
];
const activeFaq = ref<number | null>(null);
const toggleFaq = (index: number) => {
    activeFaq.value = activeFaq.value === index ? null : index;
};

// WhatsApp Logic
const showWAPopup = ref(false);
onMounted(() => {
    AOS.init({ duration: 800, once: true, offset: 100 });
    bgInterval = setInterval(() => {
        currentBgIndex.value = (currentBgIndex.value + 1) % bgImages.length;
    }, 10000); // 10 seconds

    // Show WA popup after 4 seconds
    setTimeout(() => {
        showWAPopup.value = true;

        // Auto hide after 10 seconds of being visible
        setTimeout(() => {
            showWAPopup.value = false;
        }, 10000);
    }, 4000);
});
</script>

<template>
    <Head title="Pesan Tiket | HDEX Trans Bus" />

    <div
        class="min-h-screen bg-gray-50 font-sans text-gray-900 selection:bg-[#d4af37] selection:text-white"
    >
        <!-- Navigation -->
        <header
            class="fixed inset-x-0 top-0 z-50 border-b border-gray-200 bg-white/90 shadow-sm backdrop-blur-md transition-all"
        >
            <nav
                class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8"
                aria-label="Global"
            >
                <div class="flex lg:flex-1">
                    <a href="#" class="-m-1.5 p-1.5">
                        <span class="sr-only">HDEX Trans</span>
                        <div
                            class="text-2xl font-black tracking-tighter text-gray-900"
                        >
                            HDEX<span class="text-[#d4af37]">TRANS</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex lg:gap-x-8">
                    <a
                        href="#beranda"
                        class="text-sm leading-6 font-bold text-gray-600 transition hover:text-[#d4af37]"
                        >Beranda</a
                    >
                    <a
                        href="#tentang-kami"
                        class="text-sm leading-6 font-bold text-gray-600 transition hover:text-[#d4af37]"
                        >Tentang Kami</a
                    >
                    <a
                        href="#rute"
                        class="text-sm leading-6 font-bold text-gray-600 transition hover:text-[#d4af37]"
                        >Rute & Jadwal</a
                    >
                    <a
                        href="#faq"
                        class="text-sm leading-6 font-bold text-gray-600 transition hover:text-[#d4af37]"
                        >FAQ</a
                    >
                </div>

                <div
                    class="flex flex-1 items-center justify-end gap-x-4 lg:gap-x-6"
                >
                    <template v-if="$page.props.auth?.user">
                        <template v-if="$page.props.auth.user.role === 'admin'">
                            <div
                                class="hidden text-sm font-bold text-gray-700 md:block"
                            >
                                Halo, {{ $page.props.auth.user.name }}
                            </div>
                            <Link
                                href="/dashboard"
                                class="rounded-full bg-[#d4af37] px-5 py-2 text-sm font-bold text-white shadow transition hover:-translate-y-0.5 hover:bg-[#b08d26]"
                                >Dashboard</Link
                            >
                        </template>
                        <template v-else>
                            <div class="relative">
                                <button
                                    @click="isUserMenuOpen = !isUserMenuOpen"
                                    class="flex items-center gap-2 rounded-full border border-gray-200 bg-white py-1.5 pr-4 pl-2 text-sm font-bold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    <div
                                        class="flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-gray-100 text-gray-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            class="mt-1 h-5 w-5"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    {{ $page.props.auth.user.name }}
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        class="h-4 w-4 text-gray-400"
                                        :class="{
                                            'rotate-180': isUserMenuOpen,
                                        }"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                        />
                                    </svg>
                                </button>

                                <div
                                    v-if="isUserMenuOpen"
                                    @click.away="isUserMenuOpen = false"
                                    class="absolute right-0 z-50 mt-3 w-56 origin-top-right transform rounded-2xl border border-gray-100 bg-white py-2 shadow-xl transition-all"
                                >
                                    <div
                                        class="mb-1 border-b border-gray-50 px-4 py-3"
                                    >
                                        <p
                                            class="text-sm font-bold text-gray-900"
                                        >
                                            {{ $page.props.auth.user.name }}
                                        </p>
                                        <p
                                            class="truncate text-xs text-gray-500"
                                        >
                                            {{ $page.props.auth.user.email }}
                                        </p>
                                    </div>
                                    <Link
                                        href="/settings/profile"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-[#d4af37]"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            class="h-5 w-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
                                            />
                                        </svg>
                                        Profil Saya
                                    </Link>
                                    <Link
                                        href="/user/bookings"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-[#d4af37]"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            class="h-5 w-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"
                                            />
                                        </svg>
                                        Riwayat Pesanan
                                    </Link>
                                    <div
                                        class="my-1 border-t border-gray-100"
                                    ></div>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-bold text-red-600 hover:bg-red-50"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            class="h-5 w-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"
                                            />
                                        </svg>
                                        Keluar
                                    </Link>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="text-sm leading-6 font-bold text-gray-700 transition hover:text-[#d4af37]"
                            >Log in</Link
                        >
                        <Link
                            v-if="canRegister"
                            href="/register"
                            class="rounded-full bg-[#d4af37] px-5 py-2 text-sm font-bold text-white shadow-md shadow-[#d4af37]/30 transition hover:-translate-y-0.5 hover:bg-[#b08d26]"
                            >Daftar</Link
                        >
                    </template>
                </div>
            </nav>
        </header>

        <!-- Hero Section with Slideshow Background -->
        <div
            id="beranda"
            class="relative isolate flex min-h-[90vh] flex-col justify-center overflow-hidden pt-24 pb-12 lg:pt-32 lg:pb-24"
        >
            <!-- Image Backgrounds with Fade -->
            <div class="absolute inset-0 -z-20 bg-black">
                <template v-for="(img, index) in bgImages" :key="index">
                    <img
                        :src="img"
                        class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000 ease-in-out"
                        :class="
                            currentBgIndex === index
                                ? 'opacity-100'
                                : 'opacity-0'
                        "
                        alt="Background"
                    />
                </template>
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"
                ></div>
                <div
                    class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-transparent"
                ></div>
            </div>

            <div
                class="relative z-10 mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8"
            >
                <div
                    class="mx-auto max-w-2xl text-center"
                    data-aos="zoom-in"
                    data-aos-duration="1000"
                >
                    <span
                        class="mb-6 inline-block rounded-full border border-white/30 bg-white/10 px-4 py-1.5 text-xs font-bold tracking-widest text-white uppercase backdrop-blur-sm sm:text-sm"
                        >Bus Malam Eksklusif</span
                    >
                    <h1
                        class="mb-6 text-4xl font-black tracking-tight text-white drop-shadow-2xl sm:text-6xl lg:text-7xl"
                    >
                        Perjalanan Premium<br />Tanpa Kompromi
                    </h1>
                    <p
                        class="mt-6 px-4 text-base leading-8 text-gray-200 drop-shadow-md sm:text-lg"
                    >
                        HDEX Trans memberikan pengalaman istirahat terbaik di
                        jalan. Pesan tiket Anda sekarang dan rasakan nyamannya
                        Sleeper Class kami.
                    </p>
                </div>

                <!-- Search Widget -->
                <div
                    class="relative z-20 mx-auto mt-12 max-w-5xl rounded-[2rem] border border-gray-100 bg-white/95 p-4 shadow-2xl backdrop-blur-xl sm:p-6 md:p-8"
                    data-aos="fade-up"
                    data-aos-delay="200"
                >
                    <form
                        @submit.prevent="searchTickets"
                        class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 md:grid-cols-4"
                    >
                        <div class="space-y-2">
                            <label
                                class="ml-1 block text-sm font-bold text-gray-700"
                                >Kota Asal</label
                            >
                            <select
                                v-model="form.origin"
                                @change="form.destination = ''"
                                required
                                class="bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27%2F%3E%3C%2Fsvg%3E')] w-full appearance-none rounded-xl border-gray-200 bg-white bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat px-4 py-3.5 pr-10 text-gray-900 shadow-sm transition focus:border-[#d4af37] focus:ring-[#d4af37]"
                            >
                                <option value="" disabled>Pilih asal</option>
                                <option
                                    v-for="city in validOrigins"
                                    :key="city.id"
                                    :value="city.id"
                                >
                                    {{ city.name }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="ml-1 block text-sm font-bold text-gray-700"
                                >Kota Tujuan</label
                            >
                            <select
                                v-model="form.destination"
                                :disabled="!form.origin"
                                required
                                class="bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27%2F%3E%3C%2Fsvg%3E')] w-full appearance-none rounded-xl border-gray-200 bg-white bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat px-4 py-3.5 pr-10 text-gray-900 shadow-sm transition focus:border-[#d4af37] focus:ring-[#d4af37] disabled:bg-gray-50 disabled:opacity-50"
                            >
                                <option value="" disabled>
                                    {{
                                        form.origin
                                            ? 'Pilih tujuan'
                                            : 'Pilih asal dulu'
                                    }}
                                </option>
                                <option
                                    v-for="city in validDestinations"
                                    :key="city.id"
                                    :value="city.id"
                                >
                                    {{ city.name }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="ml-1 block text-sm font-bold text-gray-700"
                                >Tanggal Keberangkatan</label
                            >
                            <input
                                type="date"
                                v-model="form.date"
                                required
                                class="w-full rounded-xl border-gray-200 bg-white px-4 py-3.5 text-gray-900 shadow-sm transition focus:border-[#d4af37] focus:ring-[#d4af37]"
                            />
                        </div>

                        <button
                            type="submit"
                            :disabled="isSearching"
                            class="w-full rounded-xl bg-[#d4af37] px-4 py-3.5 text-sm font-black text-white shadow-lg shadow-[#d4af37]/40 transition-all duration-300 hover:-translate-y-1 hover:bg-[#b08d26] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#d4af37] disabled:transform-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{
                                isSearching
                                    ? 'Mencari...'
                                    : 'Cari Tiket Sekarang'
                            }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Results Section -->
        <div
            id="search-results"
            v-if="hasSearched"
            class="bg-gray-50 py-12 sm:py-20"
        >
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Header Info -->
                <div
                    class="mb-8 border-l-4 border-[#e11d48] pl-5"
                    data-aos="fade-right"
                >
                    <h2
                        class="text-2xl font-black tracking-tight text-gray-900 uppercase sm:text-3xl"
                    >
                        {{ cities.find((c) => c.id === form.origin)?.name }}
                        <span class="mx-2 text-[#e11d48]">&rarr;</span>
                        {{
                            cities.find((c) => c.id === form.destination)?.name
                        }}
                    </h2>
                    <p class="mt-1 text-sm font-bold text-gray-500">
                        Tanggal :
                        {{
                            new Date(form.date).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric',
                            })
                        }}
                    </p>
                </div>

                <!-- Ubah Pencarian Bar -->
                <div
                    class="mb-8 flex items-center justify-between rounded-2xl border border-gray-100 bg-white px-6 py-4 shadow-sm"
                    data-aos="fade-up"
                >
                    <div
                        class="flex items-center gap-3 text-sm font-bold text-gray-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="h-4 w-4 text-[#e11d48]"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                            />
                        </svg>
                        UBAH PENCARIAN
                    </div>
                    <button
                        @click="
                            document
                                .getElementById('beranda')
                                .scrollIntoView({ behavior: 'smooth' })
                        "
                        class="flex items-center gap-1.5 rounded-lg bg-gray-50 px-4 py-1.5 text-xs font-bold text-gray-600 transition hover:bg-gray-100"
                    >
                        Edit
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-3 w-3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Time Filters -->
                <div
                    class="hide-scrollbar mb-8 flex w-full gap-3 overflow-x-auto pb-4"
                    data-aos="fade-up"
                    data-aos-delay="100"
                >
                    <button
                        class="shrink-0 rounded-full bg-[#e11d48] px-6 py-2.5 text-sm font-bold text-white shadow-md transition"
                    >
                        Semua
                    </button>
                    <button
                        class="shrink-0 rounded-full border border-gray-200 bg-white px-6 py-2.5 text-sm font-bold text-gray-500 transition hover:bg-gray-50"
                    >
                        Pagi (00:00 - 11:59)
                    </button>
                    <button
                        class="shrink-0 rounded-full border border-gray-200 bg-white px-6 py-2.5 text-sm font-bold text-gray-500 transition hover:bg-gray-50"
                    >
                        Siang (12:00 - 17:59)
                    </button>
                    <button
                        class="shrink-0 rounded-full border border-gray-200 bg-white px-6 py-2.5 text-sm font-bold text-gray-500 transition hover:bg-gray-50"
                    >
                        Malam (18:00 - 23:59)
                    </button>
                </div>

                <!-- Empty State -->
                <div
                    v-if="schedules.length === 0"
                    class="rounded-[2.5rem] border border-gray-100 bg-white py-20 text-center shadow-xl"
                    data-aos="zoom-in"
                >
                    <div
                        class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gray-50 text-gray-300"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-10 w-10"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900">
                        Jadwal Tidak Tersedia
                    </h3>
                    <p class="mt-2 text-gray-500">
                        Maaf, tidak ada jadwal keberangkatan pada tanggal ini.
                    </p>
                </div>

                <!-- Schedule Cards -->
                <div class="space-y-6">
                    <div
                        v-for="(schedule, index) in schedules"
                        :key="schedule.id"
                        class="group relative rounded-[2rem] border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl sm:p-8"
                        data-aos="fade-up"
                        :data-aos-delay="index * 100"
                    >
                        <!-- Top Header: Bus Name & Type & Seats -->
                        <div
                            class="mb-6 flex items-start justify-between gap-2 border-b border-gray-50 pb-5"
                        >
                            <div class="flex flex-col gap-2">
                                <div
                                    class="flex w-max items-center gap-2 rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-[11px] font-bold text-gray-700 sm:text-xs"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        class="h-3.5 w-3.5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"
                                        />
                                    </svg>
                                    {{ schedule.bus.name }}
                                </div>
                                <div
                                    class="flex w-max items-center gap-1 text-[10px] font-black tracking-widest uppercase sm:text-xs"
                                    :class="
                                        schedule.bus.layout_type === 'sleeper'
                                            ? 'text-[#d4af37]'
                                            : 'text-[#e11d48]'
                                    "
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="h-3.5 w-3.5"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    {{
                                        schedule.bus.layout_type === 'sleeper'
                                            ? 'SLEEPER'
                                            : 'REGULAR'
                                    }}
                                </div>
                            </div>

                            <!-- Seats availability -->
                            <div
                                class="flex flex-col items-end justify-center text-right"
                            >
                                <div
                                    class="mb-1 text-2xl leading-none font-black text-[#e11d48] sm:text-3xl"
                                >
                                    {{ schedule.available_seats
                                    }}<span
                                        class="text-xs font-bold text-gray-400 sm:text-sm"
                                        >/{{ schedule.bus.total_seats }}</span
                                    >
                                </div>
                                <div
                                    class="text-[9px] font-black tracking-widest text-gray-400 uppercase sm:text-[10px]"
                                >
                                    Tersedia
                                </div>
                            </div>
                        </div>

                        <!-- Middle: Time & Route -->
                        <div class="mb-6 flex items-center gap-3 sm:gap-6">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-[#e11d48] sm:h-14 sm:w-14 sm:rounded-2xl"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="h-5 w-5 sm:h-8 sm:w-8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <div
                                    class="mb-1 text-3xl leading-none font-black tracking-tighter text-gray-900 italic sm:text-5xl"
                                >
                                    {{ formatTime(schedule.departure_time) }}
                                </div>
                                <div
                                    class="text-xs font-bold text-gray-500 sm:text-sm"
                                >
                                    {{ schedule.bus_route.origin_city.name }}
                                    <span class="mx-1 text-[#d4af37]"
                                        >&rarr;</span
                                    >
                                    {{
                                        schedule.bus_route.destination_city.name
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- Bottom: Price & Action -->
                        <div
                            class="flex flex-col items-center gap-4 sm:flex-row sm:gap-6"
                        >
                            <div class="relative w-full flex-1 sm:w-auto">
                                <div
                                    class="flex w-full items-center justify-between rounded-2xl border border-gray-200 bg-gray-50/50 px-5 py-3 sm:px-6 sm:py-4"
                                >
                                    <span
                                        class="text-xs font-black tracking-widest text-gray-500 uppercase"
                                        >Tarif Dasar</span
                                    >
                                    <div
                                        class="text-xl font-black text-[#e11d48] sm:text-2xl"
                                    >
                                        {{ formatPrice(schedule.price) }}
                                    </div>
                                </div>
                            </div>
                            <Link
                                :href="`/schedule/${schedule.id}/seats`"
                                class="flex h-14 w-full shrink-0 items-center justify-center rounded-2xl bg-[#e11d48] text-sm font-black tracking-widest text-white uppercase shadow-lg shadow-red-200 transition-all hover:-translate-y-0.5 hover:bg-red-700 active:translate-y-0 sm:w-40"
                            >
                                Pilih Kursi &rarr;
                            </Link>
                        </div>

                        <!-- Bottom Toggle -->
                        <div class="mt-5 border-t border-gray-50 pt-5">
                            <button
                                @click="toggleDetail(schedule.id)"
                                class="flex w-full items-center justify-center gap-2 text-xs font-black tracking-widest text-gray-400 uppercase transition hover:text-[#e11d48]"
                            >
                                Detail Rute & Armada
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="3"
                                    stroke="currentColor"
                                    class="h-3 w-3 transition-transform duration-300"
                                    :class="{
                                        'rotate-180':
                                            expandedDetail === schedule.id,
                                    }"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                    />
                                </svg>
                            </button>

                            <!-- Expanded Content -->
                            <transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="transform opacity-0 -translate-y-4"
                                enter-to-class="transform opacity-100 translate-y-0"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="transform opacity-100 translate-y-0"
                                leave-to-class="transform opacity-0 -translate-y-4"
                            >
                                <div
                                    v-if="expandedDetail === schedule.id"
                                    class="mt-6 grid grid-cols-1 gap-6 pb-2 md:grid-cols-2"
                                >
                                    <div class="rounded-2xl bg-gray-50 p-6">
                                        <div
                                            class="mb-4 flex items-center gap-2 text-xs font-black tracking-widest text-gray-400 uppercase"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="2"
                                                stroke="currentColor"
                                                class="h-4 w-4"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
                                                />
                                            </svg>
                                            Detail Rute & Waktu
                                        </div>
                                        <div class="space-y-4">
                                            <div
                                                class="flex items-center justify-between text-sm font-bold"
                                            >
                                                <span class="text-gray-500"
                                                    >Titik Keberangkatan</span
                                                >
                                                <span class="text-gray-900"
                                                    >{{
                                                        schedule.bus_route
                                                            .origin_city.name
                                                    }}
                                                    ({{
                                                        formatTime(
                                                            schedule.departure_time,
                                                        )
                                                    }})</span
                                                >
                                            </div>
                                            <div
                                                class="flex items-center justify-between text-sm font-bold"
                                            >
                                                <span class="text-gray-500"
                                                    >Estimasi Kedatangan</span
                                                >
                                                <span class="text-gray-900"
                                                    >{{
                                                        schedule.bus_route
                                                            .destination_city
                                                            .name
                                                    }}
                                                    ({{
                                                        formatTime(
                                                            schedule.arrival_time,
                                                        )
                                                    }})</span
                                                >
                                            </div>
                                            <div
                                                class="flex items-center justify-between text-sm font-bold"
                                            >
                                                <span class="text-gray-500"
                                                    >Jarak Tempuh</span
                                                >
                                                <span class="text-gray-900"
                                                    >{{
                                                        schedule.bus_route
                                                            .distance
                                                    }}
                                                    KM</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-2xl bg-gray-50 p-6">
                                        <div
                                            class="mb-4 flex items-center gap-2 text-xs font-black tracking-widest text-gray-400 uppercase"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="2"
                                                stroke="currentColor"
                                                class="h-4 w-4"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"
                                                />
                                            </svg>
                                            Informasi Armada
                                        </div>
                                        <div class="space-y-4">
                                            <div
                                                class="flex items-center justify-between text-sm font-bold"
                                            >
                                                <span class="text-gray-500"
                                                    >Nama Armada</span
                                                >
                                                <span class="text-gray-900">{{
                                                    schedule.bus.name
                                                }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-between text-sm font-bold"
                                            >
                                                <span class="text-gray-500"
                                                    >Nomor Polisi</span
                                                >
                                                <span
                                                    class="rounded bg-gray-200 px-3 py-1 font-mono text-xs tracking-wider text-gray-900 uppercase"
                                                    >{{
                                                        schedule.bus
                                                            .plate_number
                                                    }}</span
                                                >
                                            </div>
                                            <div
                                                class="flex items-center justify-between text-sm font-bold"
                                            >
                                                <span class="text-gray-500"
                                                    >Tipe Kelas</span
                                                >
                                                <span
                                                    class="text-gray-900 uppercase"
                                                    >{{
                                                        schedule.bus
                                                            .layout_type ===
                                                        'sleeper'
                                                            ? 'Sleeper Class'
                                                            : 'Royal Class'
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tentang Kami -->
        <section id="tentang-kami" class="bg-white py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-1 items-center gap-12 sm:gap-16 lg:grid-cols-2"
                >
                    <div data-aos="fade-right">
                        <span
                            class="mb-2 block text-sm font-bold tracking-widest text-[#d4af37] uppercase"
                            >Tentang Kami</span
                        >
                        <h2
                            class="mb-6 text-3xl leading-tight font-black text-gray-900 sm:text-4xl"
                        >
                            Mendefinisikan Ulang Perjalanan Darat
                        </h2>
                        <p
                            class="mb-6 text-base leading-relaxed text-gray-600 sm:text-lg"
                        >
                            HDEX Trans didirikan dengan visi untuk memberikan
                            kenyamanan setara penerbangan kelas satu dalam
                            perjalanan darat antar kota. Kami berdedikasi untuk
                            memberikan pengalaman perjalanan yang aman, tepat
                            waktu, dan sangat nyaman.
                        </p>
                        <p
                            class="mb-8 text-base leading-relaxed text-gray-600 sm:text-lg"
                        >
                            Dengan armada bus terbaru, fasilitas *Sleeper
                            Class*, dan kru yang profesional, perjalanan darat
                            malam hari Anda akan terasa sekejap.
                        </p>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="border-l-4 border-[#d4af37] pl-4">
                                <div
                                    class="mb-1 text-3xl font-black text-gray-900"
                                >
                                    50+
                                </div>
                                <div
                                    class="text-sm font-bold text-gray-500 uppercase"
                                >
                                    Armada Premium
                                </div>
                            </div>
                            <div class="border-l-4 border-[#d4af37] pl-4">
                                <div
                                    class="mb-1 text-3xl font-black text-gray-900"
                                >
                                    1M+
                                </div>
                                <div
                                    class="text-sm font-bold text-gray-500 uppercase"
                                >
                                    Penumpang Puas
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative" data-aos="fade-left">
                        <div
                            class="absolute inset-0 rounded-full bg-gradient-to-tr from-[#d4af37] to-transparent opacity-10 blur-3xl"
                        ></div>
                        <img
                            src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=3538&auto=format&fit=crop"
                            alt="Bus Interior"
                            class="relative z-10 h-[400px] w-full rounded-3xl object-cover shadow-2xl sm:h-[500px]"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Fasilitas / Rute -->
        <section id="rute" class="bg-gray-50 py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <span
                    class="mb-2 block text-sm font-bold tracking-widest text-[#d4af37] uppercase"
                    data-aos="fade-up"
                    >Layanan Kami</span
                >
                <h2
                    class="mb-4 text-3xl font-black text-gray-900 sm:text-4xl"
                    data-aos="fade-up"
                    data-aos-delay="100"
                >
                    Fasilitas Kelas Satu
                </h2>
                <p
                    class="mx-auto mb-16 max-w-2xl text-base text-gray-600 sm:text-lg"
                    data-aos="fade-up"
                    data-aos-delay="200"
                >
                    Kami melayani berbagai rute di pulau Jawa dengan fasilitas
                    eksklusif yang memanjakan Anda sepanjang perjalanan.
                </p>

                <div
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 md:grid-cols-3"
                >
                    <div
                        class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-xl"
                        data-aos="fade-up"
                        data-aos-delay="100"
                    >
                        <div
                            class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 text-[#d4af37] shadow-inner"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-8 w-8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-3 text-xl font-black text-gray-900">
                            Sleeper Seat
                        </h3>
                        <p class="text-gray-500">
                            Kursi yang dapat direbahkan hingga 150 derajat,
                            dilengkapi bantal dan selimut premium untuk tidur
                            pulas.
                        </p>
                    </div>
                    <div
                        class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-xl"
                        data-aos="fade-up"
                        data-aos-delay="200"
                    >
                        <div
                            class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 text-[#d4af37] shadow-inner"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-8 w-8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-3 text-xl font-black text-gray-900">
                            Rute Terkoneksi
                        </h3>
                        <p class="text-gray-500">
                            Menjangkau kota-kota besar seperti Jakarta, Bandung,
                            Semarang, Yogyakarta, hingga Surabaya via Tol Trans
                            Jawa.
                        </p>
                    </div>
                    <div
                        class="rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-xl"
                        data-aos="fade-up"
                        data-aos-delay="300"
                    >
                        <div
                            class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 text-[#d4af37] shadow-inner"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-8 w-8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-3 text-xl font-black text-gray-900">
                            Entertainment
                        </h3>
                        <p class="text-gray-500">
                            Audio Video On Demand (AVOD) di setiap kursi, USB
                            charging port, dan WiFi berkecepatan tinggi gratis.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section
            id="faq"
            class="border-t border-gray-100 bg-white py-16 sm:py-24"
        >
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="mb-12 text-center sm:mb-16" data-aos="fade-up">
                    <span
                        class="mb-2 block text-sm font-bold tracking-widest text-[#d4af37] uppercase"
                        >FAQ</span
                    >
                    <h2
                        class="mb-4 text-3xl font-black text-gray-900 sm:text-4xl"
                    >
                        Pertanyaan Umum
                    </h2>
                </div>
                <div class="space-y-4">
                    <div
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="overflow-hidden rounded-2xl border border-gray-100 bg-gray-50 shadow-sm transition-all duration-300 hover:shadow-md"
                        data-aos="fade-up"
                        :data-aos-delay="index * 100"
                    >
                        <button
                            @click="toggleFaq(index)"
                            class="flex w-full items-center justify-between bg-white px-6 py-5 transition hover:bg-gray-50 focus:outline-none"
                        >
                            <h4
                                class="pr-4 text-left text-base font-bold text-gray-900 sm:text-lg"
                            >
                                {{ faq.q }}
                            </h4>
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2.5"
                                    stroke="currentColor"
                                    class="h-4 w-4 transform text-[#d4af37] transition-transform duration-300"
                                    :class="
                                        activeFaq === index ? 'rotate-180' : ''
                                    "
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                    />
                                </svg>
                            </div>
                        </button>
                        <div
                            v-show="activeFaq === index"
                            class="bg-white px-6 pb-5"
                        >
                            <p class="pt-2 text-gray-600">{{ faq.a }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer / Contact -->
        <footer id="kontak" class="bg-gray-900 pt-20 pb-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-16 grid grid-cols-1 gap-12 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <div
                            class="mb-6 text-3xl font-black tracking-tighter text-white"
                        >
                            HDEX<span class="text-[#d4af37]">TRANS</span>
                        </div>
                        <p
                            class="max-w-sm text-sm leading-relaxed text-gray-400 sm:text-base"
                        >
                            Memberikan pengalaman perjalanan darat terbaik
                            dengan standar pelayanan prima layaknya penerbangan
                            kelas bisnis.
                        </p>
                    </div>
                    <div>
                        <h4 class="mb-6 font-bold tracking-wider text-white">
                            PERUSAHAAN
                        </h4>
                        <ul class="space-y-4 text-sm font-medium text-gray-400">
                            <li>
                                <a
                                    href="#tentang-kami"
                                    class="transition hover:text-[#d4af37]"
                                    >Tentang Kami</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#rute"
                                    class="transition hover:text-[#d4af37]"
                                    >Layanan</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition hover:text-[#d4af37]"
                                    >Syarat & Ketentuan</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition hover:text-[#d4af37]"
                                    >Kebijakan Privasi</a
                                >
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-6 font-bold tracking-wider text-white">
                            HUBUNGI KAMI
                        </h4>
                        <ul class="space-y-4 text-sm font-medium text-gray-400">
                            <li class="flex items-center gap-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="h-5 w-5 text-[#d4af37]"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"
                                    />
                                </svg>
                                +62 812 3456 7890
                            </li>
                            <li class="flex items-center gap-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="h-5 w-5 text-[#d4af37]"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"
                                    />
                                </svg>
                                cs@hdextrans.com
                            </li>
                            <li class="flex items-center gap-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="h-5 w-5 text-[#d4af37]"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
                                    />
                                </svg>
                                Jl. Lintas Jawa No. 1, Jakarta
                            </li>
                        </ul>
                    </div>
                </div>
                <div
                    class="border-t border-gray-800 pt-8 text-center text-sm font-bold text-gray-500"
                >
                    &copy; 2026 HDEX Trans Bus. Hak cipta dilindungi.
                </div>
            </div>
        </footer>

        <!-- WhatsApp Floating Button -->
        <div
            class="fixed right-6 bottom-6 z-[60] flex flex-col items-end gap-3 sm:right-10 sm:bottom-10"
        >
            <!-- Message Bubble -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform opacity-0 translate-x-10 scale-95"
                enter-to-class="transform opacity-100 translate-x-0 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform opacity-100 translate-x-0 scale-100"
                leave-to-class="transform opacity-0 translate-x-10 scale-95"
            >
                <div
                    v-if="showWAPopup"
                    class="flex items-center gap-3 rounded-2xl border border-gray-100 bg-white px-5 py-3 shadow-2xl"
                >
                    <div
                        class="flex h-2 w-2 animate-pulse rounded-full bg-green-500"
                    ></div>
                    <p
                        class="text-sm font-black tracking-widest text-gray-700 uppercase"
                    >
                        Ada Pertanyaan? Chat Kami!
                    </p>
                    <button
                        @click="showWAPopup = false"
                        class="text-gray-400 transition hover:text-gray-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="3"
                            stroke="currentColor"
                            class="h-3 w-3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </transition>

            <a
                href="https://wa.me/6281234567890"
                target="_blank"
                class="group relative flex h-14 w-14 items-center justify-center rounded-full bg-green-500 text-white shadow-2xl shadow-green-200 transition-all duration-300 hover:scale-110 hover:bg-green-600 active:scale-95 sm:h-16 sm:w-16"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 448 512"
                    fill="currentColor"
                    class="h-8 w-8 transition-transform group-hover:rotate-12 sm:h-9 sm:w-9"
                >
                    <path
                        d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-117zm-157 338.7h-.1c-33.2 0-65.7-8.9-93.9-25.7l-6.7-4-69.8 18.3 18.7-68.1-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.5-2.8-23.2-8.5-44.2-27.2-16.4-14.6-27.4-32.7-30.6-38.2-3.2-5.6-.3-8.6 2.5-11.3 2.5-2.4 5.5-6.5 8.3-9.7 2.8-3.3 3.7-5.6 5.6-9.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.2 5.8 23.5 9.2 31.5 11.8 13.3 4.2 25.4 3.6 35 2.2 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"
                    />
                </svg>
                <!-- Notification Dot -->
                <span
                    class="absolute -top-1 -right-1 flex h-5 w-5 animate-pulse items-center justify-center rounded-full bg-red-600 text-[10px] font-black text-white ring-4 ring-gray-50"
                    >1</span
                >
            </a>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faStairs } from '@fortawesome/free-solid-svg-icons';

const byPrefixAndName = {
    fas: {
        stairs: faStairs,
    },
};

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
        form.seats = form.seats.filter((s) => s !== seatNumber);
    } else {
        form.seats.push(seatNumber);
    }
};

const isSeatBooked = (seatNumber: string) =>
    props.bookedSeats.includes(seatNumber);
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
            for (let j = 0; j < 2; j++) {
                if (seatIdx <= total) {
                    row.left.push(seatIdx.toString());
                    seatIdx++;
                }
            }
            // Right side (2 seats)
            for (let j = 0; j < 2; j++) {
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
        maximumFractionDigits: 0,
    }).format(price);
};

const proceedToBooking = () => {
    if (form.seats.length === 0) {
        alert('Silakan pilih minimal 1 kursi.');
        return;
    }

    form.post('/booking', {
        onSuccess: () => {
            // Redirect will be handled by Laravel
        },
        onError: (errors) => {
            if (errors.seats) alert(errors.seats);
        },
    });
};
</script>

<template>
    <Head title="Pilih Kursi | HDEX Trans Bus" />

    <div
        class="min-h-screen bg-gray-50 pb-32 font-sans text-gray-900 selection:bg-[#d4af37] selection:text-white"
    >
        <!-- Navigation (Same as Welcome) -->
        <header
            class="fixed inset-x-0 top-0 z-50 border-b border-gray-200 bg-white/90 shadow-sm backdrop-blur-md"
        >
            <nav
                class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8"
            >
                <div class="flex lg:flex-1">
                    <Link href="/" class="-m-1.5 p-1.5">
                        <div
                            class="text-2xl font-black tracking-tighter text-gray-900"
                        >
                            HDEX<span class="text-[#d4af37]">TRANS</span>
                        </div>
                    </Link>
                </div>

                <div
                    class="flex flex-1 items-center justify-end gap-x-4 lg:gap-x-6"
                >
                    <template v-if="auth.user">
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
                                {{ auth.user.name }}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="h-4 w-4 text-gray-400"
                                    :class="{ 'rotate-180': isUserMenuOpen }"
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
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ auth.user.name }}
                                    </p>
                                    <p class="truncate text-xs text-gray-500">
                                        {{ auth.user.email }}
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
                </div>
            </nav>
        </header>

        <main class="mx-auto max-w-7xl px-4 pt-28 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
                <!-- Info Section -->
                <div class="space-y-6 lg:col-span-4">
                    <div
                        class="rounded-[2.5rem] border border-gray-100 bg-white p-8 shadow-sm"
                    >
                        <h2
                            class="mb-6 flex items-center gap-2 text-xl font-black text-gray-900"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2.5"
                                stroke="currentColor"
                                class="h-5 w-5 text-[#e11d48]"
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
                            Detail Perjalanan
                        </h2>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="h-16 w-1.5 rounded-full bg-gradient-to-b from-[#e11d48] to-gray-200"
                                ></div>
                                <div class="space-y-4">
                                    <div>
                                        <p
                                            class="text-xs font-black tracking-widest text-gray-400 uppercase"
                                        >
                                            Keberangkatan
                                        </p>
                                        <p class="font-bold text-gray-900">
                                            {{
                                                schedule.bus_route.origin_city
                                                    .name
                                            }}
                                        </p>
                                        <p
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            {{
                                                new Date(
                                                    schedule.departure_time,
                                                ).toLocaleTimeString('id-ID', {
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                })
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs font-black tracking-widest text-gray-400 uppercase"
                                        >
                                            Kedatangan
                                        </p>
                                        <p class="font-bold text-gray-900">
                                            {{
                                                schedule.bus_route
                                                    .destination_city.name
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-50 pt-4">
                                <p
                                    class="mb-1 text-xs font-black tracking-widest text-gray-400 uppercase"
                                >
                                    Armada
                                </p>
                                <p class="font-black text-gray-900">
                                    {{ schedule.bus.name }}
                                </p>
                                <p class="text-xs font-bold text-[#d4af37]">
                                    {{
                                        schedule.bus.layout_type === 'sleeper'
                                            ? 'SLEEPER CLASS'
                                            : 'ROYAL CLASS'
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-[2.5rem] border border-gray-100 bg-white p-8 shadow-sm"
                    >
                        <h2 class="mb-6 text-xl font-black text-gray-900">
                            Keterangan
                        </h2>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="text-[#bfc3cc]">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="h-8 w-8"
                                    >
                                        <path
                                            d="M15 3H9C6.02024 3 3.54782 5.17213 3.07974 8.01937C3.21811 8.00655 3.35829 8 3.5 8C5.98528 8 8 10.0147 8 12.5V14H16V12.5C16 10.0147 18.0147 8 20.5 8C20.6417 8 20.7819 8.00655 20.9203 8.01937C20.4522 5.17213 17.9798 3 15 3ZM21 10.05C20.8384 10.0172 20.6712 10 20.5 10C19.1193 10 18 11.1193 18 12.5V17H16V16H8V17H6V12.5C6 11.1193 4.88071 10 3.5 10C3.32877 10 3.16156 10.0172 3 10.05C1.85888 10.2816 1 11.2905 1 12.5C1 13.0886 1.18173 13.5388 1.39024 14.0554C1.67083 14.7505 1.9999 15.5658 1.99978 17V20C1.99978 20.5523 2.44749 21 2.99978 21H12H21.0002C21.5525 21 22.0002 20.5523 22.0002 20V17C22.0001 15.5658 22.3292 14.7505 22.6098 14.0554C22.8183 13.5388 23 13.0886 23 12.5C23 11.2905 22.1411 10.2816 21 10.05Z"
                                        />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-gray-600"
                                    >Tersedia</span
                                >
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-[#22c55e]">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="h-8 w-8"
                                    >
                                        <path
                                            d="M15 3H9C6.02024 3 3.54782 5.17213 3.07974 8.01937C3.21811 8.00655 3.35829 8 3.5 8C5.98528 8 8 10.0147 8 12.5V14H16V12.5C16 10.0147 18.0147 8 20.5 8C20.6417 8 20.7819 8.00655 20.9203 8.01937C20.4522 5.17213 17.9798 3 15 3ZM21 10.05C20.8384 10.0172 20.6712 10 20.5 10C19.1193 10 18 11.1193 18 12.5V17H16V16H8V17H6V12.5C6 11.1193 4.88071 10 3.5 10C3.32877 10 3.16156 10.0172 3 10.05C1.85888 10.2816 1 11.2905 1 12.5C1 13.0886 1.18173 13.5388 1.39024 14.0554C1.67083 14.7505 1.9999 15.5658 1.99978 17V20C1.99978 20.5523 2.44749 21 2.99978 21H12H21.0002C21.5525 21 22.0002 20.5523 22.0002 20V17C22.0001 15.5658 22.3292 14.7505 22.6098 14.0554C22.8183 13.5388 23 13.0886 23 12.5C23 11.2905 22.1411 10.2816 21 10.05Z"
                                        />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-[#22c55e]"
                                    >Dipilih</span
                                >
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-red-500">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="h-8 w-8"
                                    >
                                        <path
                                            d="M15 3H9C6.02024 3 3.54782 5.17213 3.07974 8.01937C3.21811 8.00655 3.35829 8 3.5 8C5.98528 8 8 10.0147 8 12.5V14H16V12.5C16 10.0147 18.0147 8 20.5 8C20.6417 8 20.7819 8.00655 20.9203 8.01937C20.4522 5.17213 17.9798 3 15 3ZM21 10.05C20.8384 10.0172 20.6712 10 20.5 10C19.1193 10 18 11.1193 18 12.5V17H16V16H8V17H6V12.5C6 11.1193 4.88071 10 3.5 10C3.32877 10 3.16156 10.0172 3 10.05C1.85888 10.2816 1 11.2905 1 12.5C1 13.0886 1.18173 13.5388 1.39024 14.0554C1.67083 14.7505 1.9999 15.5658 1.99978 17V20C1.99978 20.5523 2.44749 21 2.99978 21H12H21.0002C21.5525 21 22.0002 20.5523 22.0002 20V17C22.0001 15.5658 22.3292 14.7505 22.6098 14.0554C22.8183 13.5388 23 13.0886 23 12.5C23 11.2905 22.1411 10.2816 21 10.05Z"
                                        />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-red-500"
                                    >Sudah Terisi</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seat Layout Section -->
                <div
                    class="flex flex-col items-center px-2 sm:px-0 lg:col-span-8"
                >
                    <div
                        class="relative w-full max-w-2xl overflow-hidden rounded-t-[3rem] rounded-b-[1.5rem] border border-gray-100/50 bg-white p-4 shadow-[0_40px_80px_rgba(0,0,0,0.06)] sm:overflow-visible sm:rounded-t-[5rem] sm:rounded-b-[2rem] sm:p-12 lg:rounded-t-[6rem] lg:rounded-b-[3rem] lg:p-20"
                    >
                        <!-- Dashboard Area (Front) -->
                        <div
                            class="mb-20 flex items-center justify-between px-8"
                        >
                            <div class="text-gray-500">
                                <FontAwesomeIcon
                                    :icon="byPrefixAndName.fas['stairs']"
                                    class="text-[3rem] sm:text-[4rem] lg:text-[3rem]"
                                />
                            </div>
                            <div class="text-gray-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="h-12 w-12"
                                >
                                    <circle cx="12" cy="12" r="9" />
                                    <circle cx="12" cy="12" r="2.5" />
                                    <line x1="3" y1="12" x2="9.5" y2="12" />
                                    <line x1="14.5" y1="12" x2="21" y2="12" />
                                    <line x1="12" y1="14.5" x2="12" y2="21" />
                                </svg>
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="space-y-4 sm:space-y-6">
                            <div
                                v-for="row in seatRows"
                                :key="row.id"
                                class="flex items-center justify-between px-1 sm:px-0"
                            >
                                <!-- Left side -->
                                <div class="flex gap-2 sm:gap-4 lg:gap-6">
                                    <button
                                        v-for="seat in row.left"
                                        :key="seat"
                                        @click="toggleSeat(seat)"
                                        :disabled="isSeatBooked(seat)"
                                        class="group relative flex h-12 w-12 items-center justify-center transition-all duration-300 sm:h-16 sm:w-16 lg:h-20 lg:w-20"
                                    >
                                        <svg
                                            v-if="
                                                isSeatBooked(seat) ||
                                                isSeatSelected(seat)
                                            "
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            class="h-full w-full"
                                            :style="{
                                                filter: isSeatBooked(seat)
                                                    ? 'drop-shadow(0 8px 12px rgba(239, 68, 68, 0.45))'
                                                    : 'drop-shadow(0 8px 12px rgba(34, 197, 94, 0.45))',
                                            }"
                                            :class="
                                                isSeatBooked(seat)
                                                    ? 'text-red-500'
                                                    : 'text-[#22c55e]'
                                            "
                                        >
                                            <path
                                                d="M15 3H9C6.02024 3 3.54782 5.17213 3.07974 8.01937C3.21811 8.00655 3.35829 8 3.5 8C5.98528 8 8 10.0147 8 12.5V14H16V12.5C16 10.0147 18.0147 8 20.5 8C20.6417 8 20.7819 8.00655 20.9203 8.01937C20.4522 5.17213 17.9798 3 15 3ZM21 10.05C20.8384 10.0172 20.6712 10 20.5 10C19.1193 10 18 11.1193 18 12.5V17H16V16H8V17H6V12.5C6 11.1193 4.88071 10 3.5 10C3.32877 10 3.16156 10.0172 3 10.05C1.85888 10.2816 1 11.2905 1 12.5C1 13.0886 1.18173 13.5388 1.39024 14.0554C1.67083 14.7505 1.9999 15.5658 1.99978 17V20C1.99978 20.5523 2.44749 21 2.99978 21H12H21.0002C21.5525 21 22.0002 20.5523 22.0002 20V17C22.0001 15.5658 22.3292 14.7505 22.6098 14.0554C22.8183 13.5388 23 13.0886 23 12.5C23 11.2905 22.1411 10.2816 21 10.05Z"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            class="h-full w-full text-[#bfc3cc]"
                                        >
                                            <path
                                                d="M15 3H9C6.02024 3 3.54782 5.17213 3.07974 8.01937C3.21811 8.00655 3.35829 8 3.5 8C5.98528 8 8 10.0147 8 12.5V14H16V12.5C16 10.0147 18.0147 8 20.5 8C20.6417 8 20.7819 8.00655 20.9203 8.01937C20.4522 5.17213 17.9798 3 15 3ZM21 10.05C20.8384 10.0172 20.6712 10 20.5 10C19.1193 10 18 11.1193 18 12.5V17H16V16H8V17H6V12.5C6 11.1193 4.88071 10 3.5 10C3.32877 10 3.16156 10.0172 3 10.05C1.85888 10.2816 1 11.2905 1 12.5C1 13.0886 1.18173 13.5388 1.39024 14.0554C1.67083 14.7505 1.9999 15.5658 1.99978 17V20C1.99978 20.5523 2.44749 21 2.99978 21H12H21.0002C21.5525 21 22.0002 20.5523 22.0002 20V17C22.0001 15.5658 22.3292 14.7505 22.6098 14.0554C22.8183 13.5388 23 13.0886 23 12.5C23 11.2905 22.1411 10.2816 21 10.05Z"
                                            />
                                        </svg>
                                        <span
                                            class="absolute inset-0 mt-1 flex items-center justify-center text-[10px] font-bold sm:mt-2 sm:text-[11px] lg:text-[12px]"
                                            :class="
                                                isSeatSelected(seat) ||
                                                isSeatBooked(seat)
                                                    ? 'text-white'
                                                    : 'text-gray-600'
                                            "
                                            >{{ seat }}</span
                                        >

                                        <!-- Tooltip -->
                                        <div
                                            class="pointer-events-none absolute -top-12 left-1/2 z-50 -translate-x-1/2 rounded-xl bg-[#bfc3cc] px-4 py-2 text-[11px] font-medium whitespace-nowrap text-white opacity-0 shadow-xl transition-opacity group-hover:opacity-100"
                                        >
                                            Seat: {{ seat }} |
                                            {{ formatPrice(schedule.price) }}
                                            <div
                                                class="absolute -bottom-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rotate-45 bg-[#bfc3cc]"
                                            ></div>
                                        </div>
                                    </button>
                                </div>

                                <!-- Aisle -->
                                <div
                                    class="flex h-full w-12 shrink-0 items-center justify-center sm:w-16 lg:w-28"
                                ></div>

                                <!-- Right side -->
                                <div class="flex gap-2 sm:gap-4 lg:gap-6">
                                    <button
                                        v-for="seat in row.right"
                                        :key="seat"
                                        @click="toggleSeat(seat)"
                                        :disabled="isSeatBooked(seat)"
                                        class="group relative flex h-12 w-12 items-center justify-center transition-all duration-300 sm:h-16 sm:w-16 lg:h-20 lg:w-20"
                                    >
                                        <svg
                                            v-if="
                                                isSeatBooked(seat) ||
                                                isSeatSelected(seat)
                                            "
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            class="h-full w-full"
                                            :style="{
                                                filter: isSeatBooked(seat)
                                                    ? 'drop-shadow(0 8px 12px rgba(239, 68, 68, 0.45))'
                                                    : 'drop-shadow(0 8px 12px rgba(34, 197, 94, 0.45))',
                                            }"
                                            :class="
                                                isSeatBooked(seat)
                                                    ? 'text-red-500'
                                                    : 'text-[#22c55e]'
                                            "
                                        >
                                            <path
                                                d="M15 3H9C6.02024 3 3.54782 5.17213 3.07974 8.01937C3.21811 8.00655 3.35829 8 3.5 8C5.98528 8 8 10.0147 8 12.5V14H16V12.5C16 10.0147 18.0147 8 20.5 8C20.6417 8 20.7819 8.00655 20.9203 8.01937C20.4522 5.17213 17.9798 3 15 3ZM21 10.05C20.8384 10.0172 20.6712 10 20.5 10C19.1193 10 18 11.1193 18 12.5V17H16V16H8V17H6V12.5C6 11.1193 4.88071 10 3.5 10C3.32877 10 3.16156 10.0172 3 10.05C1.85888 10.2816 1 11.2905 1 12.5C1 13.0886 1.18173 13.5388 1.39024 14.0554C1.67083 14.7505 1.9999 15.5658 1.99978 17V20C1.99978 20.5523 2.44749 21 2.99978 21H12H21.0002C21.5525 21 22.0002 20.5523 22.0002 20V17C22.0001 15.5658 22.3292 14.7505 22.6098 14.0554C22.8183 13.5388 23 13.0886 23 12.5C23 11.2905 22.1411 10.2816 21 10.05Z"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            class="h-full w-full text-[#bfc3cc]"
                                        >
                                            <path
                                                d="M15 3H9C6.02024 3 3.54782 5.17213 3.07974 8.01937C3.21811 8.00655 3.35829 8 3.5 8C5.98528 8 8 10.0147 8 12.5V14H16V12.5C16 10.0147 18.0147 8 20.5 8C20.6417 8 20.7819 8.00655 20.9203 8.01937C20.4522 5.17213 17.9798 3 15 3ZM21 10.05C20.8384 10.0172 20.6712 10 20.5 10C19.1193 10 18 11.1193 18 12.5V17H16V16H8V17H6V12.5C6 11.1193 4.88071 10 3.5 10C3.32877 10 3.16156 10.0172 3 10.05C1.85888 10.2816 1 11.2905 1 12.5C1 13.0886 1.18173 13.5388 1.39024 14.0554C1.67083 14.7505 1.9999 15.5658 1.99978 17V20C1.99978 20.5523 2.44749 21 2.99978 21H12H21.0002C21.5525 21 22.0002 20.5523 22.0002 20V17C22.0001 15.5658 22.3292 14.7505 22.6098 14.0554C22.8183 13.5388 23 13.0886 23 12.5C23 11.2905 22.1411 10.2816 21 10.05Z"
                                            />
                                        </svg>
                                        <span
                                            class="absolute inset-0 mt-1 flex items-center justify-center text-[10px] font-bold sm:mt-2 sm:text-[11px] lg:text-[12px]"
                                            :class="
                                                isSeatSelected(seat) ||
                                                isSeatBooked(seat)
                                                    ? 'text-white'
                                                    : 'text-gray-600'
                                            "
                                            >{{ seat }}</span
                                        >

                                        <!-- Tooltip -->
                                        <div
                                            class="pointer-events-none absolute -top-12 left-1/2 z-50 -translate-x-1/2 rounded-xl bg-[#bfc3cc] px-4 py-2 text-[11px] font-medium whitespace-nowrap text-white opacity-0 shadow-xl transition-opacity group-hover:opacity-100"
                                        >
                                            Seat: {{ seat }} |
                                            {{ formatPrice(schedule.price) }}
                                            <div
                                                class="absolute -bottom-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rotate-45 bg-[#bfc3cc]"
                                            ></div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Rear Area (Back) -->
                        <div class="mt-8 flex items-end justify-between px-8">
                            <div class="flex flex-col gap-6 text-gray-500">
                                <FontAwesomeIcon
                                    :icon="byPrefixAndName.fas['stairs']"
                                    class="text-[3rem] sm:text-[4rem] lg:text-[3rem]"
                                />
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="ml-1 h-10 w-10"
                                >
                                    <path
                                        d="M7 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 5.5c1.1 0 2.5.4 2.5 2v5.5H8v6H6v-6H4.5v-5.5c0-1.6 1.4-2 2.5-2zM17 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm-3.5 5.5h7l-1.5 6.5h-1v7h-2v-7h-1l-1.5-6.5z"
                                    />
                                    <line
                                        x1="12"
                                        y1="2"
                                        x2="12"
                                        y2="22"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    />
                                </svg>
                            </div>
                            <div></div>
                        </div>

                        <!-- Rear Bumper Decoration -->
                        <div
                            class="mt-8 h-4 w-full rounded-b-[2rem] bg-gray-50/50"
                        ></div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Sticky Bottom Action -->
        <div
            class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-100 bg-white/80 p-6 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 sm:flex-row"
            >
                <div class="flex items-center gap-8">
                    <div>
                        <p
                            class="mb-1 text-[10px] font-black tracking-widest text-gray-400 uppercase"
                        >
                            Total Kursi
                        </p>
                        <p class="text-xl font-black text-gray-900">
                            {{ form.seats.length }}
                            <span class="text-sm font-bold text-gray-500"
                                >dipilih</span
                            >
                        </p>
                    </div>
                    <div class="h-10 w-[1px] bg-gray-100"></div>
                    <div>
                        <p
                            class="mb-1 text-[10px] font-black tracking-widest text-gray-400 uppercase"
                        >
                            Total Bayar
                        </p>
                        <p class="text-2xl font-black text-[#22c55e]">
                            {{ formatPrice(totalPrice) }}
                        </p>
                    </div>
                </div>
                <button
                    @click="proceedToBooking"
                    :disabled="form.seats.length === 0 || form.processing"
                    class="flex w-full items-center justify-center gap-3 rounded-2xl bg-[#22c55e] px-10 py-4 text-sm font-black tracking-widest text-white uppercase shadow-xl shadow-green-100 transition hover:bg-green-600 active:scale-95 disabled:opacity-50 sm:w-auto"
                >
                    {{ form.processing ? 'Memproses...' : 'Lanjut Pembayaran' }}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2.5"
                        stroke="currentColor"
                        class="h-4 w-4"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

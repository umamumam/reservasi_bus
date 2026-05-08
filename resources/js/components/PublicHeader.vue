<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage<any>();
const isUserMenuOpen = ref(false);
const isMenuOpen = ref(false);
</script>

<template>
    <header
        class="fixed inset-x-0 top-0 z-50 border-b border-gray-200 bg-white/90 shadow-sm backdrop-blur-md transition-all"
    >
        <nav
            class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8"
            aria-label="Global"
        >
            <div class="flex lg:flex-1">
                <Link href="/" class="-m-1.5 p-1.5">
                    <span class="sr-only">HDEX Trans</span>
                    <div
                        class="text-2xl font-black tracking-tighter text-gray-900"
                    >
                        HDEX<span class="text-[#d4af37]">TRANS</span>
                    </div>
                </Link>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex lg:gap-x-8">
                <Link
                    href="/#beranda"
                    class="text-sm leading-6 font-bold text-gray-600 transition hover:text-[#d4af37]"
                    >Beranda</Link
                >
                <Link
                    href="/#tentang-kami"
                    class="text-sm leading-6 font-bold text-gray-600 transition hover:text-[#d4af37]"
                    >Tentang Kami</Link
                >
                <Link
                    href="/#rute"
                    class="text-sm leading-6 font-bold text-gray-600 transition hover:text-[#d4af37]"
                    >Rute & Jadwal</Link
                >
                <Link
                    href="/#faq"
                    class="text-sm leading-6 font-bold text-gray-600 transition hover:text-[#d4af37]"
                    >FAQ</Link
                >
            </div>

            <div
                class="flex flex-1 items-center justify-end gap-x-4 lg:gap-x-6"
            >
                <template v-if="page.props.auth?.user">
                    <template v-if="page.props.auth.user.role === 'admin'">
                        <div
                            class="hidden text-sm font-bold text-gray-700 md:block"
                        >
                            Halo, {{ page.props.auth.user.name }}
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
                                {{ page.props.auth.user.name }}
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
                                        {{ page.props.auth.user.name }}
                                    </p>
                                    <p
                                        class="truncate text-xs text-gray-500"
                                    >
                                        {{ page.props.auth.user.email }}
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
                        href="/register"
                        class="rounded-full bg-[#d4af37] px-5 py-2 text-sm font-bold text-white shadow-md shadow-[#d4af37]/30 transition hover:-translate-y-0.5 hover:bg-[#b08d26]"
                        >Daftar</Link
                    >
                </template>
            </div>
        </nav>
    </header>
</template>

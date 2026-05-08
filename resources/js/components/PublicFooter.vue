<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
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
                            <Link
                                href="/#tentang-kami"
                                class="transition hover:text-[#d4af37]"
                                >Tentang Kami</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/#rute"
                                class="transition hover:text-[#d4af37]"
                                >Layanan</Link
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
</template>

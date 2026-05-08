<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { edit } from '@/routes/appearance';
import { computed, h } from 'vue';

import PublicLayout from '@/layouts/PublicLayout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const page = usePage<any>();

defineOptions({
    layout: (h: any, pageComponent: any) => {
        const user = pageComponent.props.auth.user;
        if (user && user.role === 'admin') {
            return h(AppLayout, {
                breadcrumbs: [
                    {
                        title: 'Appearance settings',
                        href: edit(),
                    },
                ],
            }, () => h(SettingsLayout, () => pageComponent));
        }
        return h(PublicLayout, () => pageComponent);
    },
});
</script>

<template>
    <Head title="Appearance Settings" />

    <div :class="[page.props.auth.user.role === 'admin' ? '' : 'max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8']">
        <div v-if="page.props.auth.user.role !== 'admin'" class="mb-12">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 rounded bg-[#d4af37]/10 text-[#d4af37] text-[10px] font-black uppercase tracking-widest">Theme Settings</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Tampilan</h1>
            <p class="text-gray-500 mt-2 text-lg">Sesuaikan tema aplikasi sesuai dengan preferensi Anda.</p>
        </div>

        <div :class="[page.props.auth.user.role === 'admin' ? 'space-y-6' : 'bg-white rounded-[2.5rem] border border-gray-100 shadow-xl p-8 sm:p-12']">
            <Heading
                v-if="page.props.auth.user.role === 'admin'"
                variant="small"
                title="Appearance settings"
                description="Update your account's appearance settings"
            />
            
            <div v-else class="mb-10 pb-10 border-b border-gray-100">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-1 uppercase">Tema Aplikasi</h2>
                <p class="text-gray-500 font-medium">Pilih antara mode terang, gelap, atau ikuti sistem.</p>
            </div>

            <AppearanceTabs />
        </div>
    </div>
</template>

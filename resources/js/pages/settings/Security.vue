<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { ShieldCheck } from 'lucide-vue-next';
import { onUnmounted, ref, computed, h } from 'vue';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { edit } from '@/routes/security';
import { disable, enable } from '@/routes/two-factor';

import PublicLayout from '@/layouts/PublicLayout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

type Props = {
    canManageTwoFactor?: boolean;
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    canManageTwoFactor: false,
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const page = usePage<any>();

defineOptions({
    layout: (h: any, pageComponent: any) => {
        const user = pageComponent.props.auth.user;
        if (user && user.role === 'admin') {
            return h(AppLayout, {
                breadcrumbs: [
                    {
                        title: 'Security settings',
                        href: edit(),
                    },
                ],
            }, () => h(SettingsLayout, () => pageComponent));
        }
        return h(PublicLayout, () => pageComponent);
    },
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => clearTwoFactorAuthData());
</script>

<template>
    <Head title="Security Settings" />

    <div :class="[page.props.auth.user.role === 'admin' ? '' : 'max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8']">
        <div v-if="page.props.auth.user.role !== 'admin'" class="mb-12">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 rounded bg-[#d4af37]/10 text-[#d4af37] text-[10px] font-black uppercase tracking-widest">Security Settings</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Keamanan Akun</h1>
            <p class="text-gray-500 mt-2 text-lg">Pastikan akun Anda tetap aman dengan kata sandi yang kuat dan autentikasi dua faktor.</p>
        </div>

        <div :class="[page.props.auth.user.role === 'admin' ? 'space-y-6' : 'bg-white rounded-[2.5rem] border border-gray-100 shadow-xl p-8 sm:p-12 mb-12']">
            <Heading
                v-if="page.props.auth.user.role === 'admin'"
                variant="small"
                title="Update password"
                description="Ensure your account is using a long, random password to stay secure"
            />
            
            <div v-else class="mb-10 pb-10 border-b border-gray-100">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-1 uppercase">Ganti Kata Sandi</h2>
                <p class="text-gray-500 font-medium">Gunakan kombinasi simbol, angka, dan huruf kapital untuk keamanan maksimal.</p>
            </div>

            <Form
                v-bind="SecurityController.update.form()"
                :options="{
                    preserveScroll: true,
                }"
                :class="[page.props.auth.user.role === 'admin' ? 'space-y-6' : 'space-y-8']"
                v-slot="{ errors, processing }"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="grid gap-2">
                        <Label for="password" :class="[page.props.auth.user.role === 'admin' ? '' : 'text-xs font-black uppercase tracking-widest text-gray-400 ml-1']">Kata Sandi Baru</Label>
                        <PasswordInput
                            id="password"
                            name="password"
                            :class="[page.props.auth.user.role === 'admin' ? 'mt-1 block w-full' : 'w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 text-sm font-bold text-gray-900 shadow-inner focus:ring-2 focus:ring-[#d4af37] border-none']"
                            autocomplete="new-password"
                            placeholder="New password"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation" :class="[page.props.auth.user.role === 'admin' ? '' : 'text-xs font-black uppercase tracking-widest text-gray-400 ml-1']">Konfirmasi Kata Sandi</Label>
                        <PasswordInput
                            id="password_confirmation"
                            name="password_confirmation"
                            :class="[page.props.auth.user.role === 'admin' ? 'mt-1 block w-full' : 'w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 text-sm font-bold text-gray-900 shadow-inner focus:ring-2 focus:ring-[#d4af37] border-none']"
                            autocomplete="new-password"
                            placeholder="Confirm password"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <Button
                        :disabled="processing"
                        data-test="update-password-button"
                        :class="[page.props.auth.user.role === 'admin' ? '' : 'rounded-2xl bg-gray-900 px-10 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl hover:scale-105 transition-all w-full md:w-auto']"
                    >
                        Update Kata Sandi
                    </Button>
                </div>
            </Form>
        </div>

        <div v-if="canManageTwoFactor" :class="[page.props.auth.user.role === 'admin' ? 'space-y-6' : 'bg-white rounded-[2.5rem] border border-gray-100 shadow-xl p-8 sm:p-12']">
            <Heading
                v-if="page.props.auth.user.role === 'admin'"
                variant="small"
                title="Two-factor authentication"
                description="Manage your two-factor authentication settings"
            />
            
            <div v-else class="mb-10 pb-10 border-b border-gray-100">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-1 uppercase">Autentikasi 2 Faktor (2FA)</h2>
                <p class="text-gray-500 font-medium">Tingkatkan keamanan akun Anda dengan verifikasi tambahan.</p>
            </div>

            <div
                v-if="!twoFactorEnabled"
                class="flex flex-col items-start justify-start space-y-6"
            >
                <p class="text-sm text-muted-foreground font-medium max-w-2xl">
                    Ketika Anda mengaktifkan autentikasi dua faktor, Anda akan diminta memasukkan PIN aman selama login. PIN ini dapat diambil dari aplikasi pendukung TOTP di ponsel Anda (seperti Google Authenticator).
                </p>

                <div>
                    <Button v-if="hasSetupData" @click="showSetupModal = true" :class="[page.props.auth.user.role === 'admin' ? '' : 'rounded-2xl bg-emerald-600 px-10 py-4 text-xs font-black uppercase tracking-widest text-white']">
                        <ShieldCheck class="mr-2 h-4 w-4" />Lanjutkan Setup
                    </Button>
                    <Form
                        v-else
                        v-bind="enable.form()"
                        @success="showSetupModal = true"
                        #default="{ processing }"
                    >
                        <Button type="submit" :disabled="processing" :class="[page.props.auth.user.role === 'admin' ? '' : 'rounded-2xl bg-emerald-600 px-10 py-4 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-emerald-200 transition-all hover:scale-105']">
                            Aktifkan 2FA Sekarang
                        </Button>
                    </Form>
                </div>
            </div>

            <div v-else class="flex flex-col items-start justify-start space-y-6">
                <p class="text-sm text-muted-foreground font-medium max-w-2xl">
                    Autentikasi dua faktor saat ini aktif. Akun Anda jauh lebih aman sekarang.
                </p>

                <div class="relative inline">
                    <Form v-bind="disable.form()" #default="{ processing }">
                        <Button
                            variant="destructive"
                            type="submit"
                            :disabled="processing"
                            :class="[page.props.auth.user.role === 'admin' ? '' : 'rounded-2xl px-10 py-4 text-xs font-black uppercase tracking-widest']"
                        >
                            Nonaktifkan 2FA
                        </Button>
                    </Form>
                </div>

                <TwoFactorRecoveryCodes />
            </div>

            <TwoFactorSetupModal
                v-model:isOpen="showSetupModal"
                :requiresConfirmation="requiresConfirmation"
                :twoFactorEnabled="twoFactorEnabled"
            />
        </div>
    </div>
</template>

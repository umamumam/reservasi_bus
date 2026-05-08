<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed, h } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

import PublicLayout from '@/layouts/PublicLayout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const page = usePage<any>();
const user = computed(() => page.props.auth.user);

defineOptions({
    layout: (h: any, pageComponent: any) => {
        const user = pageComponent.props.auth.user;
        if (user && user.role === 'admin') {
            return h(AppLayout, {
                breadcrumbs: [
                    {
                        title: 'Profile settings',
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
    <Head title="Profile Settings" />

    <div :class="[user.role === 'admin' ? '' : 'max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8']">
        <div v-if="user.role !== 'admin'" class="mb-12">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 rounded bg-[#d4af37]/10 text-[#d4af37] text-[10px] font-black uppercase tracking-widest">Account Settings</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Profil Saya</h1>
            <p class="text-gray-500 mt-2 text-lg">Kelola informasi profil dan pengaturan keamanan akun Anda.</p>
        </div>

        <!-- Basic Information -->
        <div :class="[user.role === 'admin' ? 'flex flex-col space-y-6 mb-12' : 'bg-white rounded-[2.5rem] border border-gray-100 shadow-xl p-8 sm:p-12 mb-12']">
            <Heading
                v-if="user.role === 'admin'"
                variant="small"
                title="Profile information"
                description="Update your name, email and phone number"
            />
            
            <div v-else class="mb-10 pb-10 border-b border-gray-100">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-1 uppercase">Informasi Dasar</h2>
                <p class="text-gray-500 font-medium">Pastikan data Anda valid untuk kemudahan proses booking.</p>
            </div>

            <Form
                v-bind="ProfileController.update.form()"
                :class="[user.role === 'admin' ? 'space-y-6' : 'grid grid-cols-1 md:grid-cols-2 gap-8']"
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="name" :class="[user.role === 'admin' ? '' : 'text-xs font-black uppercase tracking-widest text-gray-400 ml-1']">Nama Lengkap</Label>
                    <Input
                        id="name"
                        :class="[user.role === 'admin' ? 'mt-1 block w-full' : 'w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 text-sm font-bold text-gray-900 shadow-inner focus:ring-2 focus:ring-[#d4af37] border-none']"
                        name="name"
                        :default-value="user.name"
                        required
                        autocomplete="name"
                        placeholder="Full name"
                    />
                    <InputError class="mt-2" :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone" :class="[user.role === 'admin' ? '' : 'text-xs font-black uppercase tracking-widest text-gray-400 ml-1']">Nomor Telepon</Label>
                    <Input
                        id="phone"
                        type="tel"
                        :class="[user.role === 'admin' ? 'mt-1 block w-full' : 'w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 text-sm font-bold text-gray-900 shadow-inner focus:ring-2 focus:ring-[#d4af37] border-none']"
                        name="phone"
                        :default-value="user.phone"
                        autocomplete="tel"
                        placeholder="Nomor telepon aktif"
                    />
                    <InputError class="mt-2" :message="errors.phone" />
                </div>

                <div class="grid gap-2 md:col-span-2">
                    <Label for="email" :class="[user.role === 'admin' ? '' : 'text-xs font-black uppercase tracking-widest text-gray-400 ml-1']">Alamat Email</Label>
                    <Input
                        id="email"
                        type="email"
                        :class="[user.role === 'admin' ? 'mt-1 block w-full' : 'w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 text-sm font-bold text-gray-900 shadow-inner focus:ring-2 focus:ring-[#d4af37] border-none']"
                        name="email"
                        :default-value="user.email"
                        required
                        autocomplete="username"
                        placeholder="Email address"
                    />
                    <InputError class="mt-2" :message="errors.email" />
                </div>

                <div v-if="mustVerifyEmail && !user.email_verified_at" class="md:col-span-2">
                    <p class="-mt-4 text-sm text-muted-foreground">
                        Your email address is unverified.
                        <Link
                            :href="send()"
                            as="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                        >
                            Click here to resend the verification email.
                        </Link>
                    </p>

                    <div
                        v-if="status === 'verification-link-sent'"
                        class="mt-2 text-sm font-medium text-green-600"
                    >
                        A new verification link has been sent to your email address.
                    </div>
                </div>

                <div class="flex items-center gap-4 md:col-span-2 pt-4">
                    <Button 
                        :disabled="processing" 
                        data-test="update-profile-button"
                        :class="[user.role === 'admin' ? '' : 'rounded-2xl bg-[#d4af37] px-10 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl shadow-[#d4af37]/20 hover:scale-105 transition-all w-full md:w-auto']"
                    >
                        Simpan Perubahan
                    </Button>
                </div>
            </Form>
        </div>

        <!-- Password Information (Only for regular users here, Admin has it in Security page) -->
        <div v-if="user.role !== 'admin'" class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl p-8 sm:p-12 mb-12">
            <div class="mb-10 pb-10 border-b border-gray-100">
                <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-1 uppercase">Keamanan Kata Sandi</h2>
                <p class="text-gray-500 font-medium">Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun.</p>
            </div>

            <Form
                v-bind="SecurityController.update.form()"
                :options="{
                    preserveScroll: true,
                }"
                class="space-y-8"
                v-slot="{ errors, processing }"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="grid gap-2">
                        <Label for="password" class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Kata Sandi Baru</Label>
                        <PasswordInput
                            id="password"
                            name="password"
                            class="w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 text-sm font-bold text-gray-900 shadow-inner focus:ring-2 focus:ring-[#d4af37] border-none"
                            autocomplete="new-password"
                            placeholder="New password"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation" class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Konfirmasi Kata Sandi</Label>
                        <PasswordInput
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full rounded-2xl border-gray-100 bg-gray-50 px-6 py-4 text-sm font-bold text-gray-900 shadow-inner focus:ring-2 focus:ring-[#d4af37] border-none"
                            autocomplete="new-password"
                            placeholder="Confirm password"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <Button
                        :disabled="processing"
                        class="rounded-2xl bg-gray-900 px-10 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl hover:scale-105 transition-all w-full md:w-auto"
                    >
                        Update Kata Sandi
                    </Button>
                </div>
            </Form>
        </div>

        <!-- Only show Delete Account for Admins (in their Dashboard settings view) -->
        <DeleteUser v-if="user.role === 'admin'" />
    </div>
</template>

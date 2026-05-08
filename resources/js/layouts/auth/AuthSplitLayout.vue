<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0"
    >
        <div
            class="relative hidden h-full flex-col bg-muted p-10 text-white lg:flex dark:border-r overflow-hidden"
        >
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img src="/images/auth-bg.png" alt="HDEX Trans Premium" class="h-full w-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-900/60 to-zinc-900/40 mix-blend-multiply" />
                <div class="absolute inset-0 bg-black/40" />
            </div>
            
            <Link
                :href="home()"
                class="relative z-20 flex items-center text-xl font-bold tracking-tight"
            >
                <AppLogoIcon class="mr-3 size-10 fill-current text-[#d4af37]" />
                <span class="uppercase">HDEX<span class="text-[#d4af37]">TRANS</span></span>
            </Link>
            
            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-4 border-l-2 border-[#d4af37] pl-6">
                    <p class="text-2xl font-light leading-snug tracking-wide text-zinc-100">
                        "Experience the pinnacle of <span class="text-[#d4af37] font-medium">visionary</span> transportation. We don't just move people; we orchestrate premium journeys."
                    </p>
                    <footer class="text-sm font-medium text-zinc-400 uppercase tracking-widest">
                        HDEX Management Board
                    </footer>
                </blockquote>
            </div>
        </div>
        <div class="lg:p-8">
            <div
                class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]"
            >
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground" v-if="description">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>

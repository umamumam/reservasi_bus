<script setup lang="ts">
import PublicHeader from '@/components/PublicHeader.vue';
import PublicFooter from '@/components/PublicFooter.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 selection:bg-[#d4af37] selection:text-white">
        <PublicHeader />
        
        <main class="pt-24 min-h-[calc(100vh-400px)]">
            <slot />
        </main>

        <PublicFooter />
    </div>
</template>

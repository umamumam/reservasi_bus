import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import AOS from 'aos';
import 'aos/dist/aos.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        const noLayoutPages = [
            'Welcome', 
            'SeatSelection', 
            'Payment', 
            'Ticket', 
            'User/Bookings/Index',
            'settings/Profile',
            'settings/Security',
            'settings/Appearance'
        ];
        
        if (noLayoutPages.includes(name)) return null;

        switch (true) {
            case name.startsWith('auth/'):
                return AuthLayout;
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// Initialize AOS
AOS.init({
    duration: 800,
    once: true,
    easing: 'ease-in-out',
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();

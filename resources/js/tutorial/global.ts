import type { DriveStep } from 'driver.js';
import { tutorialState } from '@/states/ui/tutorialState.svelte';

export const NAVBAR_STEPS: DriveStep[] = [
    {
        element: '#navbar-breadcrumbs',
        popover: {
            title: 'Navigasi Halaman',
            description: 'Penunjuk posisi Anda saat ini di dalam aplikasi.',
            side: 'bottom',
            align: 'start'
        }
    },
    {
        element: '#navbar-profile',
        popover: {
            title: 'Menu Profil',
            description: 'Kelola akun Anda, lihat profil, atau keluar dari sistem di sini.',
            side: 'bottom',
            align: 'end'
        }
    }
];

export const SIDEBAR_STEPS: DriveStep[] = [
    {
        element: '#sidebar',
        popover: {
            title: 'Menu Navigasi',
            description: 'Gunakan sidebar ini untuk berpindah antar halaman fitur platform.',
            side: 'right',
            align: 'start'
        }
    }
];

export function registerGlobalTutorials() {
    tutorialState.registerSteps({
        tourId: 'global_nav',
        group: 'global',
        priority: 1,
        steps: [...SIDEBAR_STEPS, ...NAVBAR_STEPS]
    });
}

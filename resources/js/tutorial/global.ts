import type { DriveStep } from 'driver.js';
import { tutorialState } from '@/states/ui/tutorialState.svelte';

export const NAVBAR_STEPS: DriveStep[] = [
    {
        element: '#start-page-tour',
        popover: {
            title: 'Bantuan Tutorial',
            description: 'Klik tombol ini untuk memulai tur panduan penggunaan halaman ini.',
            side: 'left',
            align: 'center',
        },
    },
    {
        element: '#navbar-breadcrumbs',
        popover: {
            title: 'Navigasi Halaman',
            description: 'Penunjuk posisi Anda saat ini di dalam aplikasi.',
            side: 'bottom',
            align: 'start',
        },
    },
    {
        element: '#navbar-profile',
        popover: {
            title: 'Menu Profil',
            description: 'Kelola akun Anda, lihat profil, atau keluar dari sistem di sini.',
            side: 'bottom',
            align: 'end',
        },
    },
];

export const SIDEBAR_STEPS: DriveStep[] = [
    {
        element: '#sidebar',
        popover: {
            title: 'Menu Navigasi',
            description: 'Gunakan sidebar ini untuk berpindah antar halaman fitur platform.',
            side: 'right',
            align: 'start',
        },
    },
    {
        element: '#sidebar-mahasiswa-dashboard',
        popover: {
            title: 'Beranda',
            description: 'Kembali ke halaman utama/dashboard.',
            side: 'right',
            align: 'start',
        },
    },
    {
        element: '#sidebar-materials',
        popover: {
            title: 'Materi',
            description: 'Lihat dan pelajari semua materi pembelajaran.',
            side: 'right',
            align: 'start',
        },
    },
    {
        element: '#materials-submenu',
        popover: {
            title: 'Menu Materi',
            description: 'Daftar sub-materi dengan progress belajar.',
            side: 'right',
            align: 'start',
        },
    },
    {
        element: '#sidebar-quiz',
        popover: {
            title: 'Latihan soal',
            description: 'Kerjakan soal untuk menguji pemahaman.',
            side: 'right',
            align: 'start',
        },
    },
    {
        element: '#sidebar-leaderboard',
        popover: {
            title: 'Leaderboard',
            description: 'Lihat peringkat dan prestasi mahasiswa.',
            side: 'right',
            align: 'start',
        },
    },
    {
        element: '#sidebar-profile',
        popover: {
            title: 'Profil Saya',
            description: 'Kelola informasi akun dan lihat statistik.',
            side: 'right',
            align: 'start',
        },
    },
    {
        element: '#sidebar-tutorial-button',
        popover: {
            title: 'Bantuan Tutorial',
            description: 'Mulai tur panduan untuk halaman ini.',
            side: 'right',
            align: 'start',
        },
    },
    {
        element: '#sidebar-logout-button',
        popover: {
            title: 'Logout',
            description: 'Keluar dari sistem akun Anda.',
            side: 'right',
            align: 'start',
        },
    },
];

export function registerGlobalTutorials() {
    tutorialState.registerSteps({
        tourId: 'global_nav',
        group: 'global',
        priority: 1,
        steps: [...SIDEBAR_STEPS, ...NAVBAR_STEPS],
    });
}

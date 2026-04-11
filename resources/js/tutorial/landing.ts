import { tutorialState } from '@/states/ui/tutorialState.svelte';

export function registerLandingTutorials() {
    tutorialState.registerSteps({
        tourId: 'landing',
        steps: [
            {
                element: '#hero-section',
                popover: {
                    title: 'Selamat Datang di Oopedia',
                    description:
                        'Platform e-learning adaptif untuk Penguasaan Pemrograman Berorientasi Objek.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#feature-adaptive',
                popover: {
                    title: 'Belajar Adaptif',
                    description: 'Materi menyesuaikan dengan kemampuan Anda secara otomatis.',
                    side: 'top',
                    align: 'center',
                },
            },
            {
                element: '#cta-register',
                popover: {
                    title: 'Mulai Sekarang',
                    description: 'Daftar sekarang untuk mencoba pengalaman belajar yang baru.',
                    side: 'bottom',
                    align: 'center',
                },
            },
        ],
    });
}

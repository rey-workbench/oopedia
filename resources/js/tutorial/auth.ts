import { tutorialState } from '@/states/ui/tutorialState.svelte';

export function registerAuthTutorials() {
    tutorialState.registerSteps({
        tourId: 'auth_login',
        steps: [
            {
                element: '#login-form-container',
                popover: {
                    title: 'Selamat Datang Kembali!',
                    description:
                        'Silahkan masuk menggunakan email dan password yang telah terdaftar.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#email',
                popover: {
                    title: 'Email Anda',
                    description: 'Masukkan alamat email yang Anda gunakan saat mendaftar.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: '#password',
                popover: {
                    title: 'Kata Sandi',
                    description: 'Pastikan kata sandi Anda benar dan bersifat rahasia.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: '#login-submit-btn',
                popover: {
                    title: 'Masuk Sekarang',
                    description: 'Klik tombol ini untuk memulai perjalanan belajar Anda.',
                    side: 'bottom',
                    align: 'center',
                },
            },
        ],
    });

    tutorialState.registerSteps({
        tourId: 'auth_register',
        steps: [
            {
                element: '#register-form-container',
                popover: {
                    title: 'Registrasi Akun',
                    description:
                        'Lengkapi data diri Anda untuk bergabung dalam komunitas belajar Oopedia.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '#name',
                popover: {
                    title: 'Nama Lengkap',
                    description:
                        'Gunakan nama asli untuk kebutuhan pencetakan sertifikat nantinya.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: '#email',
                popover: {
                    title: 'Email',
                    description: 'Masukkan alamat email aktif untuk proses verifikasi.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                element: '#register-submit-btn',
                popover: {
                    title: 'Buat Akun',
                    description:
                        'Selesaikan pendaftaran dan nikmati fitur-fitur pembelajaran kami.',
                    side: 'bottom',
                    align: 'center',
                },
            },
        ],
    });
}

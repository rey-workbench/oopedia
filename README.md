# Oopedia.com - Platform Pembelajaran Interaktif

Oopedia adalah platform e-learning modern yang dirancang untuk memberikan pengalaman belajar yang interaktif dan personal. Versi terbaru ini menggunakan stack teknologi terkini untuk performa yang optimal dan user experience yang premium.

## 🚀 Tech Stack

- **Backend:** [Laravel 12.x](https://laravel.com)
- **Frontend:** [Svelte 5](https://svelte.dev) dengan [Inertia.js](https://inertiajs.com)
- **Styling:** [Tailwind CSS 4](https://tailwindcss.com)
- **Database:** MySQL
- **Build Tool:** [Vite](https://vitejs.dev)

## ✨ Fitur Utama

- **Adaptive Learning Engine:** Sistem cerdas yang menyesuaikan materi dan soal berdasarkan kemampuan pengguna.
- **Inertia.js Integration:** Pengalaman Single Page Application (SPA) tanpa kerumitan framework API terpisah.
- **Svelte Components:** Interface yang ringan, cepat, dan reaktif.
- **Admin & Student Dashboard:** Manajemen konten dan pemantauan progres yang komprehensif.

## 🛠️ Instalasi

Pastikan Anda memiliki PHP >= 8.2, Node.js, dan Composer yang terinstal.

1. **Clone Repository**

    ```bash
    git clone https://github.com/rey-workbench/oopedia.git
    ```

2. **Instal Dependensi PHP**

    ```bash
    composer install
    ```

3. **Instal Dependensi Frontend**

    ```bash
    pnpm install
    ```

4. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Migrasi & Seed Database**

    ```bash
    php artisan migrate --seed
    ```

6. **Jalankan Aplikasi**
   Buka dua terminal terpisah:

    ```bash
    # Terminal 1: Backend
    php artisan serve

    # Terminal 2: Frontend
    pnpm run dev
    ```

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan akademik (Skripsi) dan berlisensi MIT.

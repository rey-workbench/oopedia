# Components Directory Structure

Direktori ini berisi komponen-komponen Blade yang dapat digunakan kembali di seluruh aplikasi.

## Struktur Direktori

```
components/
├── admin/              # Komponen khusus untuk admin
│   └── tutorial.blade.php
├── forms/              # Komponen form yang dapat digunakan kembali
│   ├── checkbox.blade.php
│   ├── form-group.blade.php
│   └── select.blade.php
├── head/               # Komponen untuk bagian <head>
│   └── tinymce-config.blade.php
├── layouts/            # Layout dasar aplikasi
│   └── base.blade.php  # Layout unified untuk admin & mahasiswa
├── navigation/         # Komponen navigasi
│   ├── footer.blade.php
│   ├── navbar.blade.php  # Navbar unified (admin & mahasiswa)
│   └── sidebar.blade.php # Sidebar unified (admin & mahasiswa)
├── ui/                 # Komponen UI dasar
│   ├── alert.blade.php
│   ├── badge.blade.php
│   ├── button.blade.php
│   ├── card.blade.php
│   └── input.blade.php
├── layout.blade.php    # Layout lama untuk admin (Material Dashboard)
└── loading-overlay.blade.php
```

## Penggunaan

### Layout Components

#### Base Layout (Unified)
Layout baru yang mendukung admin dan mahasiswa:
```blade
<x-layouts.base title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <!-- Content here -->
</x-layouts.base>
```

#### Legacy Layout (Admin Only)
Layout lama untuk admin (Material Dashboard):
```blade
<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <!-- Content here -->
</x-layout>
```

### Navigation Components

#### Navbar (Unified)
Navbar yang otomatis menyesuaikan dengan role user:
```blade
<x-navigation.navbar titlePage="Dashboard" />
```

Props:
- `titlePage`: Judul halaman untuk breadcrumb (admin)
- `role`: Role user (otomatis terdeteksi)

#### Sidebar (Unified)
Sidebar yang otomatis menyesuaikan dengan role user:
```blade
<x-navigation.sidebar />
```

#### Footer
```blade
<x-navigation.footer />
```

### Form Components

#### Form Group
```blade
<x-forms.form-group 
    label="Nama" 
    name="name" 
    type="text" 
    :value="old('name')" 
    required 
/>
```

#### Select
```blade
<x-forms.select 
    label="Role" 
    name="role" 
    :options="$roles" 
    :selected="old('role')" 
    required 
/>
```

#### Checkbox
```blade
<x-forms.checkbox 
    label="Setuju dengan syarat dan ketentuan" 
    name="agree" 
    :checked="old('agree')" 
/>
```

### UI Components

#### Alert
```blade
<x-ui.alert type="success" message="Data berhasil disimpan!" />
<x-ui.alert type="danger" message="Terjadi kesalahan!" />
<x-ui.alert type="info" message="Informasi penting" />
```

#### Badge
```blade
<x-ui.badge color="primary">New</x-ui.badge>
<x-ui.badge color="success">Active</x-ui.badge>
<x-ui.badge color="danger">Inactive</x-ui.badge>
```

#### Button
```blade
<x-ui.button type="submit" color="primary">Simpan</x-ui.button>
<x-ui.button type="button" color="secondary">Batal</x-ui.button>
```

#### Card
```blade
<x-ui.card title="Judul Card">
    <!-- Card content -->
</x-ui.card>
```

#### Input
```blade
<x-ui.input 
    name="email" 
    type="email" 
    placeholder="Email" 
    :value="old('email')" 
/>
```

## Catatan Penting

### Komponen yang Sudah Dihapus
File-file berikut sudah dihapus karena tidak terpakai:
- `components/footers/auth.blade.php`
- `components/footers/guest.blade.php`
- `components/navbars/cpy.php`
- `components/navbars/navs/auth.blade.php`
- `components/navbars/navs/authcpy.php`
- `components/navbars/navs/guest.blade.php`
- `components/navbars/sidebar.blade.php`

### Komponen Mahasiswa (Terpisah)
Komponen khusus mahasiswa masih berada di:
- `mahasiswa/components/navbar.blade.php` - Navbar khusus mahasiswa (legacy)
- `mahasiswa/components/sidebar.blade.php` - Sidebar khusus mahasiswa (legacy)
- `mahasiswa/partials/` - Partial views untuk mahasiswa

**Catatan**: Komponen di `mahasiswa/components/` masih digunakan oleh `mahasiswa/layouts/app.blade.php`. 
Untuk migrasi ke komponen unified, gunakan `<x-navigation.navbar />` dan `<x-navigation.sidebar />`.

## Migrasi dari Komponen Lama

### Dari Mahasiswa Layout ke Unified Layout

**Sebelum:**
```blade
@extends('mahasiswa.layouts.app')

@section('content')
    <!-- Content -->
@endsection
```

**Sesudah:**
```blade
<x-layouts.base title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <x-navigation.navbar />
    <x-navigation.sidebar />
    
    <main class="main-content">
        <!-- Content -->
    </main>
</x-layouts.base>
```

### Dari Admin Layout ke Unified Layout

**Sebelum:**
```blade
<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <!-- Content -->
</x-layout>
```

**Sesudah:**
```blade
<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
    <!-- Content -->
</x-layouts.app>
```

## CSS Files

Komponen-komponen ini menggunakan file CSS berikut:
- `public/css/components.css` - Styling untuk komponen UI
- `public/css/forms.css` - Styling untuk komponen form
- `public/css/navigation.css` - Styling untuk komponen navigasi

## PHP Component Classes

Beberapa komponen memiliki class PHP di `app/View/Components/`:
- `app/View/Components/Layouts/Base.php`
- `app/View/Components/Navigation/Navbar.php`
- `app/View/Components/Navigation/Sidebar.php`
- `app/View/Components/Head/TinymceConfig.php`

Class-class ini menangani logika komponen seperti:
- Deteksi role user
- Generasi menu items
- Pengecekan route aktif
- Dan lain-lain

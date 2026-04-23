<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kelulusan - {{ $student_name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800">
    <!-- Kontainer Utama (A4 Landscape: 297mm x 210mm) -->
    <div class="relative flex h-[210mm] w-[297mm] items-center justify-center p-10 box-border">

        <!-- Border Luar Bertema Tier -->
        <div class="absolute inset-10 border-4 pointer-events-none" style="border-color: {{ $tier_color }};"></div>

        <!-- Konten Utama -->
        <div class="relative flex h-full w-full flex-col items-center bg-white p-16 text-center shadow-sm">

            <!-- Logo Oopedia -->
            <div class="absolute top-10 left-10">
                <img src="{{ $logo_image }}" class="h-14 w-auto" alt="Logo">
            </div>

            <!-- Judul Utama -->
            <div class="mt-10">
                <h1 class="text-5xl font-black tracking-[0.1em] text-slate-900 uppercase">
                    Sertifikat Kelulusan
                </h1>
                <p class="mt-2 text-lg font-bold tracking-widest text-slate-400 uppercase">
                    Certificate of Completion
                </p>
            </div>

            <!-- Teks Penerima -->
            <div class="mt-12">
                <p class="text-xl font-medium italic text-slate-500">diberikan kepada:</p>
                    <h2 class="text-7xl font-black tracking-tight text-slate-900" style="border-bottom: 4px solid {{ $tier_color }};">
                        {{ $student_name }}
                    </h2>
            </div>

            <!-- Detail Kelulusan -->
            <div class="mt-8 max-w-3xl">
                <p class="text-xl font-medium leading-relaxed text-slate-600">
                    atas kelulusannya pada kelas <span class="font-bold text-slate-900">{{ $material_title }}</span>
                </p>
                <p class="mt-1 text-lg font-medium text-slate-500">
                    pada tanggal {{ $date }}
                </p>
            </div>

            <!-- Bagian Footer (Seal, Signatures, QR) -->
            <div class="mt-auto flex w-full items-end justify-between px-10">

                <!-- Sisi Kiri: Tanda Tangan & Seal -->
                <div class="flex items-end gap-12 text-left">
                    <!-- Seal "Approved by Industry" -->
                    <div class="mb-4">
                        <svg width="100" height="150" viewBox="0 0 100 150">
                            <path d="M25 100 L25 150 L50 130 L75 150 L75 100" fill="{{ $tier_color }}" />
                            <path d="M35 100 L35 140 L50 125 L65 140 L65 100" fill="{{ $tier_color_dark }}" />
                            <circle cx="50" cy="70" r="45" fill="{{ $tier_color }}" stroke="#ffffff" stroke-width="2" />
                            <circle cx="50" cy="70" r="38" fill="none" stroke="#ffffff" stroke-width="1"
                                stroke-dasharray="2 2" />
                            <text x="50" y="62" text-anchor="middle" fill="#ffffff" font-size="8"
                                font-weight="bold">APPROVED</text>
                            <text x="50" y="72" text-anchor="middle" fill="#ffffff" font-size="8"
                                font-weight="bold">BY</text>
                            <text x="50" y="82" text-anchor="middle" fill="#ffffff" font-size="8"
                                font-weight="bold">INDUSTRY</text>
                        </svg>
                    </div>

                    <!-- Tanda Tangan Dr. Oopedia -->
                    <div class="mb-6 min-w-[280px]">
                        <p class="mb-1 text-sm font-bold text-slate-600">Malang, {{ $date }}</p>
                    <div class="h-32 flex items-center">
                        <img src="{{ $sign_image }}" class="h-28 w-auto mix-blend-multiply" alt="Signature">
                    </div>
                        <div class="border-t border-slate-400 pt-3">
                            <p class="text-lg font-bold text-slate-900">Eka Larasati, S.T. M.T</p>
                            <p class="text-sm font-medium text-slate-500">Ketua Program • OOPedia Indonesia</p>
                            <p class="mt-2 text-[10px] italic text-slate-400">
                                Sertifikat ini berlaku hingga
                                {{ \Carbon\Carbon::now()->addYears(3)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan: QR Code Verifikasi -->
                <div class="mb-6 max-w-[250px] text-right">
                    <div
                        class="ml-auto mb-4 flex h-24 w-24 items-center justify-center border border-slate-200 bg-slate-50 p-2">
                        <!-- Mock QR Code menggunakan SVG -->
                        <svg width="100%" height="100%" viewBox="0 0 24 24" fill="#1e293b">
                            <path
                                d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-3 0h2v2h-2v-2zm3 3h3v2h-3v-2zm-3 0h2v2h-2v-2zm3 3h3v2h-3v-2zm-3 0h2v2h-2v-2z">
                            </path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold leading-tight text-slate-400 uppercase">
                            Scan QR Code untuk verifikasi portfolio
                        </p>
                        <p class="text-[10px] italic text-slate-700">
                            https://oopedia.id/verify/{{ $material_id }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
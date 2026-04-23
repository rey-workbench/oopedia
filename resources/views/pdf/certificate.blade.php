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

            <!-- Logo & Branding Oopedia -->
            <div class="absolute top-10 left-10 flex items-center gap-4">
                <img src="{{ $logo_image }}" class="h-14 w-auto" alt="Logo">
                <div class="flex flex-col text-left border-l-2 pl-4" style="border-color: rgba(255, 208, 63);">
                    <span class="text-2xl font-black tracking-tighter text-slate-900 leading-none">
                        <span style="color: rgb(240, 182, 7);">OOP</span>edia
                    </span>
                    <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400 uppercase leading-none mt-1">
                        OOP e-Learning
                    </span>
                </div>
            </div>

            <!-- Judul Utama -->
            <div class="mt-14">
                <h1 class="whitespace-nowrap text-5xl font-black tracking-[0.15em] text-slate-900 uppercase">
                    Sertifikat Kelulusan
                </h1>
                <p class="mt-2 text-lg font-bold tracking-[0.3em] text-slate-400 uppercase opacity-70">
                    Certificate of Completion
                </p>
            </div>

            <!-- Teks Penerima -->
            <div class="mt-10">
                <p class="text-xl font-medium italic text-slate-400 mb-2">diberikan kepada:</p>
                <h2 class="text-6xl font-black tracking-tight text-slate-900 px-8 inline-block"
                    style="border-bottom: 4px solid {{ $tier_color }};">
                    {{ $student_name }}
                </h2>
            </div>

            <!-- Detail Kelulusan -->
            <div class="mt-6 max-w-3xl">
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
                        <p class="mt-6 text-xs font-bold text-slate-500">Malang, {{ $date }}</p>
                        <div class="h-32 flex items-center">
                            <img src="{{ $sign_image }}" class="h-28 w-auto mix-blend-multiply" alt="Signature">
                        </div>
                        <div class="border-t border-slate-400 pt-3">
                            <p class="text-lg font-bold text-slate-900">Eka Larasati Amalia, S.T. M.T</p>
                            <p class="text-sm font-medium text-slate-500">Ketua Program • OOPedia Indonesia</p>
                            <p class="mt-2 text-[10px] italic text-slate-400">
                                Sertifikat ini berlaku hingga
                                {{ \Carbon\Carbon::now()->addYears(3)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-6 max-w-[250px] text-right">
                    <div
                        class="ml-auto mb-4 flex h-32 w-32 items-center justify-center border border-slate-200 bg-slate-50 p-2 shadow-sm">
                        <img src="{{ $qr_code }}" class="h-full w-full" alt="QR Code">
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] italic text-slate-700">
                            ID: {{ $material_id }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
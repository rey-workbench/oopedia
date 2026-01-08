@php
    $currentYear = date('Y');
@endphp

<footer class="mt-auto py-12 border-t border-slate-100 bg-white/50 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            {{-- Brand Section --}}
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="OOPedia" class="h-8 w-auto opacity-80">
                <p class="text-sm font-black italic tracking-tighter text-slate-400">OOPEDIA <span class="mx-2 font-normal text-slate-300">|</span> <span class="font-bold text-slate-500">SYSTEM</span></p>
            </div>

            {{-- Copyright Section --}}
            <div class="text-center md:text-right">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                    &copy; {{ $currentYear }} <span class="text-slate-900 italic font-black">OOPEDIA TEAM</span>. SEMUA HAK DILINDUNGI.
                </p>
            </div>
        </div>
    </div>
</footer>

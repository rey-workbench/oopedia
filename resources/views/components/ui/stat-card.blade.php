@props([
    'title' => '',
    'value' => '',
    'icon' => 'fas fa-chart-line',
    'variant' => 'primary',
    'footer' => null,
])

@php
    $variants = [
        'primary' => [
            'bg' => 'bg-white',
            'iconBg' => 'bg-slate-900',
            'iconText' => 'text-white',
            'accent' => 'bg-blue-600',
        ],
        'success' => [
            'bg' => 'bg-white',
            'iconBg' => 'bg-emerald-50',
            'iconText' => 'text-emerald-500',
            'accent' => 'bg-emerald-500',
        ],
        'warning' => [
            'bg' => 'bg-white',
            'iconBg' => 'bg-amber-50',
            'iconText' => 'text-amber-500',
            'accent' => 'bg-amber-500',
        ],
        'danger' => [
            'bg' => 'bg-white',
            'iconBg' => 'bg-rose-50',
            'iconText' => 'text-rose-500',
            'accent' => 'bg-rose-500',
        ],
    ];

    $config = $variants[$variant] ?? $variants['primary'];
@endphp

<div {{ $attributes->merge(['class' => "group relative overflow-hidden rounded-[2.5rem] bg-white border border-slate-100 p-8 shadow-2xl shadow-slate-200/50 transition-all duration-500 hover:shadow-blue-100/50 hover:border-blue-100 hover:-translate-y-1"]) }}>
    {{-- Decorative Accent --}}
    <div class="absolute top-0 right-0 w-32 h-32 {{ $config['accent'] }} opacity-[0.03] rounded-bl-full -mr-10 -mt-10 transition-transform duration-700 group-hover:scale-110"></div>
    
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">{{ $title }}</p>
            <h3 class="text-3xl font-bold tracking-tight text-slate-900">{{ $value }}</h3>
        </div>
        
        <div class="w-14 h-14 rounded-2xl {{ $config['iconBg'] }} flex items-center justify-center shadow-lg shadow-current/10 border border-black/5 transition-transform duration-500 group-hover:rotate-6">
            <i class="{{ $icon }} text-xl {{ $config['iconText'] }}"></i>
        </div>
    </div>

    @if($footer || $slot->isNotEmpty())
        <div class="mt-6 pt-6 border-t border-slate-50 flex items-center gap-2">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">
                {!! $footer ?? $slot !!}
            </div>
        </div>
    @endif
</div>

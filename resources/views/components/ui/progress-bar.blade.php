@props([
    'value' => 0,
    'max' => 100,
    'variant' => 'primary',
    'size' => 'md',
    'label' => null,
    'showPercentage' => true,
])

@php
    $percentage = $max > 0 ? min(($value / $max) * 100, 100) : 0;

    $variants = [
        'primary' => 'bg-slate-900',
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-rose-500',
        'info' => 'bg-blue-600',
    ];

    $sizes = [
        'xs' => 'h-1.5',
        'sm' => 'h-2.5',
        'md' => 'h-4',
        'lg' => 'h-6',
    ];

    $barClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($label || $showPercentage)
        <div class="flex justify-between items-end mb-3">
            @if($label)
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 italic">{{ $label }}</span>
            @endif
            @if($showPercentage)
                <span class="text-sm font-black italic tracking-tighter text-slate-900">{{ number_format($percentage, 0) }}%</span>
            @endif
        </div>
    @endif

    <div class="{{ $sizeClass }} w-full bg-slate-100 rounded-full overflow-hidden p-1 shadow-inner ring-1 ring-slate-200/50">
        <div
            class="h-full {{ $barClass }} rounded-full transition-all duration-1000 ease-out relative group overflow-hidden shadow-lg shadow-current/20"
            style="width: {{ $percentage }}%"
            role="progressbar"
            aria-valuenow="{{ $value }}"
            aria-valuemin="0"
            aria-valuemax="{{ $max }}"
        >
            {{-- Shine effect --}}
            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
        </div>
    </div>
</div>

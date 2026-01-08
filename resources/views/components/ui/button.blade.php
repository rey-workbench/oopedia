@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold tracking-tight transition-all duration-300 active:scale-95 disabled:opacity-50 disabled:pointer-events-none rounded-2xl';

    $variants = [
        'primary' => 'bg-slate-900 text-white shadow-xl shadow-slate-200 hover:bg-blue-600 hover:shadow-blue-200',
        'secondary' => 'bg-white text-slate-900 border-2 border-slate-100 hover:border-blue-600 hover:text-blue-600 shadow-sm',
        'danger' => 'bg-rose-500 text-white shadow-xl shadow-rose-100 hover:bg-rose-600',
        'success' => 'bg-emerald-500 text-white shadow-xl shadow-emerald-100 hover:bg-emerald-600',
        'warning' => 'bg-amber-400 text-amber-950 shadow-xl shadow-amber-100 hover:bg-amber-500',
        'ghost' => 'text-slate-500 hover:text-blue-600 hover:bg-blue-50',
        'outline' => 'bg-transparent border-2 border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-white',
    ];

    $sizes = [
        'sm' => 'px-5 py-2.5 text-[10px]',
        'md' => 'px-8 py-3.5 text-xs',
        'lg' => 'px-10 py-5 text-sm',
        'xl' => 'px-12 py-6 text-base',
    ];

    $classes = "{$baseClasses} " . ($variants[$variant] ?? $variants['primary']) . " " . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }} {{ $slot->isEmpty() ? '' : 'mr-3' }} transition-transform group-hover:-translate-x-1"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }} {{ $slot->isEmpty() ? '' : 'ml-3' }} transition-transform group-hover:translate-x-1"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }} {{ $slot->isEmpty() ? '' : 'mr-3' }}"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }} {{ $slot->isEmpty() ? '' : 'ml-3' }}"></i>
        @endif
    </button>
@endif

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
    $variants = [
        'primary' => 'btn-component--primary',
        'secondary' => 'btn-component--secondary',
        'danger' => 'btn-component--danger',
        'success' => 'btn-component--success',
        'outline' => 'btn-component--outline',
    ];

    $sizes = [
        'sm' => 'btn-component--sm',
        'md' => '',
        'lg' => 'btn-component--lg',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? '';

    $classes = collect([
        'btn-component',
        $variantClass,
        $sizeClass,
    ])->filter()->implode(' ');
@endphp

@if($href)
    <a 
        href="{{ $href }}" 
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled) aria-disabled="true" tabindex="-1" @endif
    >
        @if($icon && $iconPosition === 'left')
            <span class="btn-component__icon material-icons">{{ $icon }}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="btn-component__icon btn-component__icon--right material-icons">{{ $icon }}</span>
        @endif
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled) disabled @endif
    >
        @if($icon && $iconPosition === 'left')
            <span class="btn-component__icon material-icons">{{ $icon }}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="btn-component__icon btn-component__icon--right material-icons">{{ $icon }}</span>
        @endif
    </button>
@endif

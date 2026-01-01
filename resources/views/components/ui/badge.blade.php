@props([
    'variant' => 'primary',
    'pill' => false,
])

@php
    $variants = [
        'primary' => 'badge-component--primary',
        'secondary' => 'badge-component--secondary',
        'success' => 'badge-component--success',
        'danger' => 'badge-component--danger',
        'warning' => 'badge-component--warning',
        'info' => 'badge-component--info',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];

    $classes = collect([
        'badge-component',
        $variantClass,
        $pill ? 'badge-component--pill' : '',
    ])->filter()->implode(' ');
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>

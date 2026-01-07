@props([
    'variant' => 'info',
    'dismissible' => true,
    'icon' => null,
])

@php
    $variants = [
        'success' => 'alert-component--success',
        'danger' => 'alert-component--danger',
        'warning' => 'alert-component--warning',
        'info' => 'alert-component--info',
    ];

    $defaultIcons = [
        'success' => 'check_circle',
        'danger' => 'error',
        'warning' => 'warning',
        'info' => 'info',
    ];

    $variantClass = $variants[$variant] ?? $variants['info'];
    $alertIcon = $icon ?? ($defaultIcons[$variant] ?? 'info');

    $classes = collect([
        'alert-component',
        $variantClass,
    ])->filter()->implode(' ');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    @if($dismissible)
        <x-ui.button type="button" class="alert-component__close" onclick="this.parentElement.remove()" aria-label="Close" icon="close" variant="link" />
    @endif

    <div class="alert-content-wrapper">
        @if($alertIcon)
            <span class="alert-component__icon material-icons">{{ $alertIcon }}</span>
        @endif
        <div>{{ $slot }}</div>
    </div>
</div>

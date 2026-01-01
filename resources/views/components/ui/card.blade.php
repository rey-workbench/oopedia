@props([
    'variant' => 'default',
    'shadow' => true,
    'hover' => true,
])

@php
    $classes = collect([
        'card-component',
        !$shadow ? 'shadow-none' : '',
        !$hover ? 'card-component--no-hover' : '',
    ])->filter()->implode(' ');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @isset($header)
        <div class="card-component__header">
            {{ $header }}
        </div>
    @endisset

    <div class="card-component__body">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-component__footer">
            {{ $footer }}
        </div>
    @endisset
</div>

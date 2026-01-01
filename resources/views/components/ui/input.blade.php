@props([
    'type' => 'text',
    'name' => '',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'rows' => 4,
])

@php
    $inputValue = old($name, $value);
    $hasError = $errors->has($name);
    
    $classes = collect([
        'input-component',
        $type === 'textarea' ? 'input-component--textarea' : '',
        $hasError ? 'input-component--error' : '',
    ])->filter()->implode(' ');
@endphp

@if($type === 'textarea')
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
    >{{ $inputValue }}</textarea>
@else
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $inputValue }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
    />
@endif

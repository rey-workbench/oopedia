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

    $baseClasses = 'w-full px-6 py-4 bg-white border border-slate-100 rounded-2xl font-bold tracking-tight text-slate-900 transition-all duration-300 placeholder:text-slate-300 placeholder:font-medium placeholder:italic';
    $focusClasses = 'focus:outline-none focus:border-blue-600 focus:shadow-2xl focus:shadow-blue-100/50';
    $errorClasses = $hasError ? 'border-rose-200 bg-rose-50/20 focus:border-rose-400 focus:shadow-rose-100' : '';
    $disabledClasses = $disabled || $readonly ? 'bg-slate-50 cursor-not-allowed text-slate-400 border-none shadow-none' : 'shadow-xl shadow-slate-100/50';

    $classes = "{$baseClasses} {$focusClasses} {$errorClasses} {$disabledClasses}";
@endphp

@if($type === 'textarea')
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => $classes . ' resize-none min-h-[120px]']) }}
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

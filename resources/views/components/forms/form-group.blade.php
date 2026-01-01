@props([
    'label' => '',
    'name' => '',
    'required' => false,
    'helpText' => null,
])

@php
    $hasError = $errors->has($name);
@endphp

<div {{ $attributes->merge(['class' => 'form-group-component']) }}>
    @if($label)
        <label 
            for="{{ $name }}" 
            class="form-group-component__label {{ $required ? 'form-group-component__label--required' : '' }}"
        >
            {{ $label }}
        </label>
    @endif

    {{ $slot }}

    @error($name)
        <span class="form-group-component__error">{{ $message }}</span>
    @enderror

    @if($helpText)
        <span class="form-group-component__help">{{ $helpText }}</span>
    @endif
</div>

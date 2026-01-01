@props([
    'name' => '',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Pilih...',
    'required' => false,
    'disabled' => false,
])

@php
    $selectedValue = old($name, $selected);
    $hasError = $errors->has($name);
    
    $classes = collect([
        'select-component',
        $hasError ? 'select-component--error' : '',
    ])->filter()->implode(' ');
@endphp

<select
    name="{{ $name }}"
    id="{{ $name }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if($required) required @endif
    @if($disabled) disabled @endif
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    
    @foreach($options as $value => $label)
        <option 
            value="{{ $value }}" 
            @if((string) $selectedValue === (string) $value) selected @endif
        >
            {{ $label }}
        </option>
    @endforeach
</select>

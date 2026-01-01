@props([
    'name' => '',
    'label' => '',
    'checked' => false,
    'value' => '1',
    'disabled' => false,
])

@php
    $isChecked = old($name, $checked);
@endphp

<label {{ $attributes->merge(['class' => 'checkbox-component']) }}>
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        class="checkbox-component__input"
        @if($isChecked) checked @endif
        @if($disabled) disabled @endif
    />
    @if($label)
        <span class="checkbox-component__label">{{ $label }}</span>
    @endif
</label>

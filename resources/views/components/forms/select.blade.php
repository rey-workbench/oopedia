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
    
    $baseClasses = 'w-full px-6 py-4 bg-white border border-slate-100 rounded-2xl font-bold tracking-tight text-slate-900 transition-all duration-300 appearance-none';
    $focusClasses = 'focus:outline-none focus:border-blue-600 focus:shadow-2xl focus:shadow-blue-100/50';
    $errorClasses = $hasError ? 'border-rose-200 bg-rose-50/20' : '';
    $disabledClasses = $disabled ? 'bg-slate-50 cursor-not-allowed text-slate-400' : 'shadow-xl shadow-slate-100/50';

    $classes = "{$baseClasses} {$focusClasses} {$errorClasses} {$disabledClasses}";
@endphp

<div class="relative group">
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
    >
        @if($placeholder)
            <option value="" disabled {{ !$selectedValue ? 'selected' : '' }}>{{ $placeholder }}</option>
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
    
    {{-- Custom Arrow --}}
    <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 transition-transform group-hover:translate-y-[-40%]">
        <i class="fas fa-chevron-down text-xs"></i>
    </div>
</div>

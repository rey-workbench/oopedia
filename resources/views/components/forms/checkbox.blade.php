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

<label {{ $attributes->merge(['class' => 'flex items-center gap-3 cursor-pointer group']) }}>
    <div class="relative flex items-center">
        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            class="peer h-6 w-6 cursor-pointer appearance-none rounded-lg border-2 border-slate-200 transition-all checked:bg-blue-600 checked:border-blue-600 hover:border-blue-300 focus:ring-4 focus:ring-blue-100"
            @if($isChecked) checked @endif
            @if($disabled) disabled @endif
        />
        <i class="fas fa-check absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></i>
    </div>
    
    @if($label)
        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">
            {{ $label }}
        </span>
    @endif
</label>

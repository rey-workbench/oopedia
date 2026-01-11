@props([
  'variant' => 'default',
  'shadow' => true,
  'hover' => true,
  'padding' => 'p-8',
])

@php
  $baseClasses = 'bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden transition-all duration-500';
  $shadowClasses = $shadow ? 'shadow-2xl shadow-slate-200/50' : '';
  $hoverClasses = $hover ? 'hover:shadow-blue-100/50 hover:border-blue-100 hover:-translate-y-1' : '';
  
  $classes = "{$baseClasses} {$shadowClasses} {$hoverClasses}";
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
  @isset($header)
    <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
      <div class="w-full">
        {{ $header }}
      </div>
    </div>
  @endisset

  <div class="{{ $padding }}">
    {{ $slot }}
  </div>

  @isset($footer)
    <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
      {{ $footer }}
    </div>
  @endisset
</div>

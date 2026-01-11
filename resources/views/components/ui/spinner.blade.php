@props([
  'size' => 'md',
  'variant' => 'primary',
])

@php
  $sizes = [
    'xs' => 'w-4 h-4 border-2',
    'sm' => 'w-6 h-6 border-2',
    'md' => 'w-10 h-10 border-[3px]',
    'lg' => 'w-16 h-16 border-4',
    'xl' => 'w-24 h-24 border-[6px]',
  ];

  $variants = [
    'primary' => 'border-slate-100 border-t-blue-600',
    'white' => 'border-white/20 border-t-white',
    'slate' => 'border-slate-800/10 border-t-slate-800',
  ];

  $sizeClass = $sizes[$size] ?? $sizes['md'];
  $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<div {{ $attributes->merge(['class' => "inline-block rounded-full animate-spin {$sizeClass} {$variantClass}"]) }} role="status">
  <span class="sr-only">Loading...</span>
</div>

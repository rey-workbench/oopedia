@props([
  'variant' => 'info',
  'dismissible' => false,
  'icon' => null,
])

@php
  $variants = [
    'success' => [
      'bg' => 'bg-emerald-50',
      'border' => 'border-emerald-100',
      'text' => 'text-emerald-700',
      'iconBg' => 'bg-emerald-500',
      'icon' => 'fas fa-check-circle',
    ],
    'warning' => [
      'bg' => 'bg-amber-50',
      'border' => 'border-amber-100',
      'text' => 'text-amber-700',
      'iconBg' => 'bg-amber-500',
      'icon' => 'fas fa-exclamation-triangle',
    ],
    'danger' => [
      'bg' => 'bg-rose-50',
      'border' => 'border-rose-100',
      'text' => 'text-rose-700',
      'iconBg' => 'bg-rose-500',
      'icon' => 'fas fa-shield-virus',
    ],
    'info' => [
      'bg' => 'bg-blue-50',
      'border' => 'border-blue-100',
      'text' => 'text-blue-700',
      'iconBg' => 'bg-blue-500',
      'icon' => 'fas fa-info-circle',
    ],
  ];

  $config = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => "{$config['bg']} {$config['border']} {$config['text']} p-4 rounded-3xl border flex items-center gap-4 shadow-xl shadow-slate-200/50 animate-in slide-in-from-top-4 duration-500"]) }} role="alert">
  <div class="{{ $config['iconBg'] }} text-white w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-current/20">
    <i class="{{ $icon ?? $config['icon'] }}"></i>
  </div>
  
  <div class="flex-1 font-bold text-sm leading-relaxed">
    {{ $slot }}
  </div>

  @if($dismissible)
    <button type="button" class="p-2 hover:bg-black/5 rounded-xl transition-colors shrink-0" onclick="this.parentElement.remove()">
      <i class="fas fa-xmark"></i>
    </button>
  @endif
</div>

@props([
  'classes' => '',
])

<div {{ $attributes->merge(['class' => 'input-group input-group-outline ' . $classes]) }}>
  {{ $slot }}
</div>

@props([
  'title' => '',
  'subtitle' => null,
  'centered' => false,
])

<div {{ $attributes->merge(['class' => ($centered ? 'text-center' : '') . ' mb-12']) }}>
  <h1 class="text-4xl md:text-5xl font-extrabold tracking-widest text-slate-900 leading-tight">
    {{ $title }}
  </h1>
  
  <div class="flex items-center gap-2 mt-4 {{ $centered ? 'justify-center' : '' }}">
    <div class="h-1.5 w-12 bg-blue-600 rounded-full"></div>
    <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
    <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
  </div>

  @if($subtitle)
    <p class="mt-6 text-slate-500 font-medium leading-relaxed max-w-3xl {{ $centered ? 'mx-auto' : '' }}">
      {{ $subtitle }}
    </p>
  @endif

  @if(isset($actions) || $slot->isNotEmpty())
    <div class="mt-8 flex flex-wrap gap-4 {{ $centered ? 'justify-center' : '' }}">
      {{ $actions ?? '' }}
      {{ $slot }}
    </div>
  @endif
</div>

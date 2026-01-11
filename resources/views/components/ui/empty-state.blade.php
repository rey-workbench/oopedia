@props([
  'icon' => 'fas fa-ghost',
  'title' => 'Belum Ada Data',
  'message' => 'Sepertinya tidak ada yang bisa ditampilkan di sini saat ini.',
  'actionText' => null,
  'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-20 px-6 rounded-[3rem] bg-white border-2 border-dashed border-slate-100']) }}>
  <div class="w-32 h-32 rounded-3xl bg-slate-50 flex items-center justify-center mb-8 shadow-inner ring-1 ring-slate-100">
    <i class="{{ $icon }} text-5xl text-slate-300"></i>
  </div>

  <h3 class="text-3xl font-bold tracking-widest text-slate-900 mb-4 text-center">
    {{ $title }}
  </h3>

  <p class="text-slate-500 font-medium text-center max-w-sm leading-relaxed mb-10">
    {{ $message }}
  </p>

  @if($actionText && $actionHref)
    <x-ui.button :href="$actionHref" variant="primary">
      {{ $actionText }}
    </x-ui.button>
  @endif

  {{ $slot }}
</div>

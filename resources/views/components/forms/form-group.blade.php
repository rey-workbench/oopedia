@props([
    'label' => '',
    'name' => '',
    'required' => false,
    'helpText' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-4 mb-6']) }}>
    @if($label)
        <label for="{{ $name }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 italic">
            {{ $label }}
            @if($required)
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse" title="Required"></span>
            @endif
        </label>
    @endif

    <div class="relative">
        {{ $slot }}
    </div>

    @error($name)
        <p class="mt-2 text-[10px] font-black italic tracking-tighter text-rose-500 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-100 animate-in slide-in-from-top-1">
            <i class="fas fa-triangle-exclamation mr-1.5"></i>
            {{ $message }}
        </p>
    @enderror

    @if($helpText)
        <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">
            {{ $helpText }}
        </p>
    @endif
</div>

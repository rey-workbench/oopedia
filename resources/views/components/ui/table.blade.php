@props([
  'headers' => null,
  'bodyClass' => 'divide-y divide-slate-100',
  'tableClass' => '',
])

<div class="overflow-x-auto">
  <table {{ $attributes->merge(['class' => "w-full text-left border-collapse {$tableClass}"]) }}>
    @if($headers)
      <thead>
        <tr>
          @foreach($headers as $header)
            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500 border-b border-slate-50 whitespace-nowrap">
              {{ $header }}
            </th>
          @endforeach
        </tr>
      </thead>
    @elseif(isset($thead))
      <thead>
        {{ $thead }}
      </thead>
    @endif
    
    <tbody class="{{ $bodyClass }}">
      {{ $slot }}
    </tbody>
  </table>
</div>

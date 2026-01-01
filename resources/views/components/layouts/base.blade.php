@props([
    'title' => 'OOPEDIA',
    'theme' => 'default',
    'meta' => [],
    'bodyClass' => ''
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-head.meta :title="$title" :meta="$meta">
        {{ $head ?? '' }}
        @stack('head')
    </x-head.meta>
    
    <x-head.styles :theme="$theme">
        {{ $styles ?? '' }}
        @stack('css')
    </x-head.styles>
</head>
<body class="{{ $bodyClass }}">
    {{-- Main Content Slot --}}
    {{ $slot }}

    {{-- Loading Overlay Component --}}
    <x-ui.loading-overlay />

    <x-head.scripts :theme="$theme">
        {{ $scripts ?? '' }}
        @stack('js')
        @stack('scripts')
    </x-head.scripts>
</body>
</html>

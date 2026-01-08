<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'OOPEDIA' }}</title>

    <x-head.styles theme="guest">
        {{ $styles ?? '' }}
        @stack('css')
    </x-head.styles>
</head>
<body class="{{ $bodyClass ?? '' }}">

    {{ $slot }}

    <x-head.scripts>
        {{ $scripts ?? '' }}
        @stack('js')
    </x-head.scripts>
</body>
</html>

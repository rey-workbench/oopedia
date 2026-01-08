@props([
    'title' => 'OOPEDIA',
    'description' => 'OOPEDIA - Platform Pembelajaran Object-Oriented Programming',
    'keywords' => 'OOP, Programming, Learning, Java, PHP',
    'author' => 'OOPEDIA Team'
])

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title }}</title>

{{-- Favicon --}}

{{-- SEO Meta Tags --}}
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="{{ $author }}">

{{-- Open Graph Meta Tags --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">

{{ $slot }}

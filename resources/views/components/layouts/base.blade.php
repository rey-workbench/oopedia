@props([
    'title' => 'OOPEDIA',
    'theme' => 'default',
    'meta' => [],
    'bodyClass' => ''
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Custom meta tags --}}
    @foreach($meta as $name => $content)
        <meta name="{{ $name }}" content="{{ $content }}">
    @endforeach
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    
    <title>{{ $title }}</title>
    
    {{-- Common Fonts --}}
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    
    {{-- Bootstrap CSS --}}
    @if($theme !== 'admin')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    
    {{-- Main App CSS --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    {{-- Component System CSS --}}
    <link href="{{ asset('css/components.css') }}" rel="stylesheet">
    
    {{-- Theme-specific CSS --}}
    @if($theme === 'admin')
        {{-- Admin Theme (Material Dashboard) --}}
        <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet">
        <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.min.css') }}?v=3.0.0" rel="stylesheet">
        <link href="{{ asset('css/admin-theme.css') }}" rel="stylesheet">
    @else
        {{-- Student Theme --}}
        <link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
    @endif
    
    {{-- Loading Overlay CSS --}}
    <link href="{{ asset('css/loading-overlay.css') }}" rel="stylesheet">
    
    {{-- IntroJS CSS for tutorials --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.0.1/introjs.min.css">
    
    {{-- TinyMCE --}}
    <script src="https://cdn.tiny.cloud/1/9iw2xqwn1593xsb15d6xpi0y41mtrets5ms0l5s8kekdgf63/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    {{-- Common Styles --}}
    <style>
        /* TinyMCE Editor Improvements */
        .tox-tinymce {
            min-height: 400px !important;
            margin-bottom: 20px;
        }

        /* Card Content Improvements */
        .card {
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Text Content Handling */
        .materi-description, 
        .question-content,
        .answer-content {
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            hyphens: auto;
            max-width: 100%;
        }

        /* Content Display Styles */
        .content-display {
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            max-width: 100%;
            padding: 15px;
        }

        .content-display img {
            max-width: 100%;
            height: auto;
        }

        .content-display pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }

        .content-display code {
            background: #f5f5f5;
            padding: 2px 4px;
            border-radius: 4px;
        }

        /* Dashboard Card Improvements */
        .materi-card {
            height: 100%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .materi-card:hover {
            transform: translateY(-5px);
        }

        .materi-card-body {
            padding: 1.5rem;
        }

        .materi-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #344767;
        }

        .materi-description {
            font-size: 0.875rem;
            color: #67748e;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    

    
    {{-- Additional head elements slot --}}
    {{ $head ?? '' }}
    
    {{-- Additional styles slot --}}
    {{ $styles ?? '' }}
    
    {{-- Stack for additional head content --}}
    @stack('head')
</head>
<body class="{{ $bodyClass }}">
    {{-- Main Content Slot --}}
    {{ $slot }}

    {{-- Loading Overlay Component --}}
    <x-ui.loading-overlay />

    {{-- Core JavaScript --}}
    @if($theme === 'admin')
        {{-- Admin Theme Scripts --}}
        <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
        <script src="{{ asset('assets/js/material-dashboard.min.js') }}?v=3.0.0"></script>
    @else
        {{-- Student Theme Scripts --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endif
    
    {{-- IntroJS for tutorials --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.0.1/intro.min.js"></script>
    
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    {{-- TinyMCE Initialization --}}
    <script>
        tinymce.init({
            selector: 'textarea.tinymce',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            menubar: false,
            height: 400,
            content_style: `
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
                    font-size: 16px;
                    line-height: 1.6;
                    color: #333;
                    margin: 15px;
                }
                p { margin: 0 0 1em 0; }
                img { max-width: 100%; height: auto; }
                pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
                code { background: #f5f5f5; padding: 2px 4px; border-radius: 4px; }
            `,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    </script>
    
    {{-- Main App JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
    
    @if($theme === 'admin')
    {{-- Admin-specific scrollbar initialization --}}
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = { damping: '0.5' };
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    @endif
    
    {{-- Additional scripts slot --}}
    {{ $scripts ?? '' }}
    
    {{-- Stack for additional JS --}}
    @stack('js')
    @stack('scripts')
</body>
</html>

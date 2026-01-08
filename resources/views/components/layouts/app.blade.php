@props([
    'title' => 'OOPEDIA',
    'theme' => null,
    'meta' => [],
    'showNavbar' => null,
    'showSidebar' => null,
    'showFooter' => false,
    'bodyClass' => '',
    'fullWidth' => false
])

@php
    $isAuthenticated = auth()->check();
    $userRole = $isAuthenticated ? auth()->user()->role_id : null;
    $isAdminRole = $isAuthenticated && in_array($userRole, [1, 2]);
    $isStudentRole = $isAuthenticated && in_array($userRole, [3, 4]);
    
    if (!$theme) {
        $theme = $isAdminRole ? 'admin' : ($isStudentRole ? 'mahasiswa' : 'default');
    }
    
    if ($showNavbar === null) {
        $showNavbar = $theme !== 'admin';
    }
    
    if ($showSidebar === null) {
        $showSidebar = true;
    }

    $finalBodyClass = "min-h-screen bg-gray-50 font-sans text-slate-900 antialiased {$bodyClass}";
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
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
<body class="{{ $finalBodyClass }}">
    <div id="app" class="relative flex min-h-screen">
        {{-- Sidebar --}}
        @if($showSidebar && ($isAdminRole || $isStudentRole))
            <x-navigation.sidebar />
            <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden hidden transition-opacity duration-300"></div>
        @endif

        {{-- Main Wrapper --}}
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 {{ ($showSidebar && ($isAdminRole || $isStudentRole)) ? 'lg:ml-64' : '' }}">
            
            {{-- Navbar --}}
            @if($showNavbar)
                <x-navigation.navbar titlePage="{{ $title }}" />
            @endif

            {{-- Flash Messages --}}
            <div class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none max-w-sm w-full">
                @foreach(['success', 'error', 'info', 'warning'] as $type)
                    @if(session($type))
                        <div class="pointer-events-auto animate-in slide-in-from-right-full duration-500">
                            <x-ui.alert :variant="$type === 'error' ? 'danger' : $type" :dismissible="true">
                                {{ session($type) }}
                            </x-ui.alert>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Main Content --}}
            <main class="flex-1 w-full {{ $fullWidth ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8' }}">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            @if($showFooter)
                <x-navigation.footer />
            @endif
        </div>
    </div>

    {{-- Universal UI Components --}}
    <x-ui.loading-overlay />

    <x-head.scripts :theme="$theme">
        {{ $scripts ?? '' }}
        @stack('js')
        @stack('scripts')
    </x-head.scripts>
</body>
</html>

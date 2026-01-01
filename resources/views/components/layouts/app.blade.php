{{--
    Main Application Layout - Entry Point (Role-Aware)
    
    A flexible, role-aware layout component that serves as the main entry point.
    Automatically detects user role and applies appropriate theme, navigation, etc.
    Uses x-layouts.base for the HTML shell.
    
    Props:
    - title: Page title (default: 'OOPEDIA')
    - theme: Theme override ('admin', 'mahasiswa', or auto-detect)
    - showNavbar: Whether to show navbar (default: auto based on role)
    - showSidebar: Whether to show sidebar (default: auto based on role)
    - showFooter: Whether to show footer (default: false)
    - bodyClass: Additional CSS classes for body element
--}}

@props([
    'title' => 'OOPEDIA',
    'theme' => null,
    'showNavbar' => null,
    'showSidebar' => null,
    'showFooter' => false,
    'bodyClass' => '',
    'fullWidth' => false  // For auth pages - no container wrapper
])

@php
    // Auto-detect user role and set appropriate defaults
    $isAuthenticated = auth()->check();
    $userRole = $isAuthenticated ? auth()->user()->role_id : null;
    
    // Role detection helpers
    $isAdminRole = $isAuthenticated && in_array($userRole, [1, 2]); // Superadmin & Admin
    $isStudentRole = $isAuthenticated && in_array($userRole, [3, 4]); // Mahasiswa & Guest
    $isGuest = !$isAuthenticated || $userRole === 4;
    
    // Auto-detect theme if not specified
    if (!$theme) {
        if ($isAdminRole) {
            $theme = 'admin';
        } elseif ($isStudentRole) {
            $theme = 'mahasiswa';
        } else {
            $theme = 'default';
        }
    }
    
    // Auto-detect navbar visibility if not specified
    if ($showNavbar === null) {
        $showNavbar = $theme !== 'admin'; // Show navbar for all roles by default EXCEPT admin (who manage it manually)
    }
    
    // Auto-detect sidebar visibility if not specified
    if ($showSidebar === null) {
        $showSidebar = $theme !== 'admin'; // Show sidebar for all roles by default EXCEPT admin
    }
    
    // Add theme class to body
    $bodyClass = trim("theme-{$theme} {$bodyClass}");
    if ($isAdminRole) {
        $bodyClass .= ' admin-layout';
    } elseif ($isStudentRole) {
        $bodyClass .= ' student-layout';
    }
@endphp

<x-layouts.base :title="$title" :theme="$theme" :body-class="$bodyClass">
    <x-slot:styles>
        {{ $styles ?? '' }}
    </x-slot:styles>

    {{-- Navbar --}}
    @if($showNavbar)
        <x-navigation.navbar titlePage="{{ $title }}" />
    @endif
    
    @if($fullWidth || $theme === 'admin')
        {{-- Full Width Mode (Auth Pages) or Admin Layout - No Container Wrapper --}}
        
        {{-- Flash Messages Overlay for Admin/FullWidth --}}
        @if(session('success'))
            <div class="fixed-top mt-3 d-flex justify-content-center" style="z-index: 9999; pointer-events: none;">
                <div class="col-10 col-md-6 col-lg-4" style="pointer-events: auto;">
                    <x-ui.alert type="success">
                        {{ session('success') }}
                    </x-ui.alert>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="fixed-top mt-3 d-flex justify-content-center" style="z-index: 9999; pointer-events: none; top: 60px;">
                <div class="col-10 col-md-6 col-lg-4" style="pointer-events: auto;">
                    <x-ui.alert type="danger">
                        {{ session('error') }}
                    </x-ui.alert>
                </div>
            </div>
        @endif

        {{ $slot }}
    @else
        {{-- Standard Mode (App Pages) - With Container --}}
        <div class="container">
            {{-- Sidebar --}}
            @if($showSidebar)
                <x-navigation.sidebar />
            @endif
            
            {{-- Main Content --}}
            <main class="main-content">
                {{-- Flash Messages Inline for Student Layout --}}
                @if(session('success'))
                    <x-ui.alert type="success">
                        {{ session('success') }}
                    </x-ui.alert>
                @endif
                
                @if(session('error'))
                    <x-ui.alert type="danger">
                        {{ session('error') }}
                    </x-ui.alert>
                @endif
                
                @if(session('info'))
                    <x-ui.alert type="info">
                        {{ session('info') }}
                    </x-ui.alert>
                @endif
                
                @if(session('warning'))
                    <x-ui.alert type="warning">
                        {{ session('warning') }}
                    </x-ui.alert>
                @endif
                
                {{-- Page Content --}}
                {{ $slot }}
            </main>
        </div>
    @endif
    
    {{-- Footer --}}
    @if($showFooter)
        <x-navigation.footer />
    @endif
    
    <x-slot:scripts>
        {{ $scripts ?? '' }}
    </x-slot:scripts>
</x-layouts.base>

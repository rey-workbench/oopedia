{{--
    Unified Footer Component
    
    A footer that adapts to authenticated and guest states.
    
    Props:
    - variant: Footer style (default/minimal/fixed)
--}}

@props([
    'variant' => 'default'
])

@php
    $isAuthenticated = auth()->check();
    $isAdmin = $isAuthenticated && in_array(auth()->user()->role_id, [1, 2]);
    $isStudent = $isAuthenticated && auth()->user()->role_id === 3;
    $currentYear = date('Y');
@endphp

@if($isAdmin)
    {{-- Admin Footer --}}
    <footer class="footer py-4">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="copyright text-center text-sm text-muted text-lg-start">
                        © {{ $currentYear }} <span class="font-weight-bold">OOPEDIA</span> - Sistem Pembelajaran OOP
                    </div>
                </div>
                <div class="col-lg-6">
                    <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link text-muted">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.materials.index') }}" class="nav-link text-muted">Materi</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.students.index') }}" class="nav-link text-muted">Mahasiswa</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

@elseif($isStudent || $variant === 'student')
    {{-- Student Footer --}}
    <footer class="footer py-3 {{ $variant === 'minimal' ? 'footer-minimal' : '' }}">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-center">
                <div class="col-12 text-center">
                    <div class="copyright text-sm text-muted">
                        © {{ $currentYear }} <span class="font-weight-bold">OOPEDIA</span> - Platform Pembelajaran Object-Oriented Programming
                    </div>
                </div>
            </div>
        </div>
    </footer>



@else
    {{-- Guest Footer (for login/register pages) --}}
    <footer class="footer {{ $variant === 'fixed' ? 'position-absolute bottom-footer' : '' }} py-2 w-100 {{ $variant === 'fixed' ? 'z-index-1' : '' }}">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-12 text-center">
                    <div class="copyright text-sm {{ $variant === 'fixed' ? 'text-white' : 'text-muted' }}">
                        © {{ $currentYear }} <span class="font-weight-bold">OOPEDIA</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @if($variant === 'fixed')
    @endif
@endif

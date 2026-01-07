<x-layouts.guest bodyClass="bg-gray-200">
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <!-- Navbar (Hidden/Commented out to match login) -->
                {{-- <x-navigation.navbar /> --}}
            </div>
        </div>
    </div>
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100 d-flex justify-content-center align-items-center"
            style="background-image: url('{{ asset('images/background-log.jpg') }}');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Daftar Akun</h4>
                                    <div class="row mt-3">
                                        <h6 class='text-white text-center'>
                                            Bergabunglah dengan OOPedia
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('register') }}" class="text-start">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Nama</label>
                                        <div class="input-group input-group-outline">
                                            <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                        </div>
                                        @error('name')
                                        <p class='text-danger inputerror'>{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <div class="input-group input-group-outline">
                                            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                        </div>
                                        @error('email')
                                        <p class='text-danger inputerror'>{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div class="input-group input-group-outline">
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                        @error('password')
                                        <p class='text-danger inputerror'>{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Konfirmasi Password</label>
                                        <div class="input-group input-group-outline">
                                            <input type="password" class="form-control" name="password_confirmation">
                                        </div>
                                    </div>
                                    
                                    <!-- Checkbox untuk mendaftar sebagai dosen -->
                                    <div class="form-check form-switch d-flex align-items-center mb-3">
                                        <input class="form-check-input" type="checkbox" name="register_as_admin" id="register_as_admin" value="1" {{ old('register_as_admin') ? 'checked' : '' }}>
                                        <label class="form-check-label mb-0 ms-3" for="register_as_admin">
                                            Daftar sebagai Dosen <span class="badge bg-warning text-dark ms-1" style="font-size: 0.6rem;">Approval</span>
                                        </label>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">DAFTAR</button>
                                    </div>
                                    <p class="mt-4 text-sm text-center">
                                        Sudah memiliki akun?
                                        <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">Masuk</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Footer if needed --}}
        </div>
    </main>
    @push('js')
        <script src="{{ asset('assets') }}/js/jquery.min.js"></script>
        {{-- <script src="{{ asset('js/auth/register.js') }}"></script> --}}
    @endpush
    @push('css')
        <link rel="stylesheet" href="{{ asset('css/themes/auth.css') }}">
    @endpush
</x-layouts.guest>
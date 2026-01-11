<x-layouts.app title="OOPEDIA" bodyClass="g-sidenav-show bg-gray-200">
  <x-navigation.sidebar activePage="pending-users" />
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <x-navigation.navbar titlePage="Admin Pending" />
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
          <br><br>

            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Dosen Menunggu Persetujuan</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="p-4">
                @if(session('success'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <x-ui.button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" variant="ghost" />
                  </div>
                @endif
                
                @if(session('error'))
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <x-ui.button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" variant="ghost" />
                  </div>
                @endif

                <!-- Tampilan untuk admin yang menunggu persetujuan -->
                <div class="container py-5">
                  <div class="row justify-content-center">
                    <div class="col-md-8">
                      <div class="card">
                        <div class="card-header bg-warning text-white">Menunggu Persetujuan</div>
                        <div class="card-body">
                          <p>Akun admin Anda sedang menunggu persetujuan dari superadmin.</p>
                          <p>Silakan coba login kembali nanti.</p>
                          
                          <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <x-ui.button type="submit" variant="danger" icon="logout">Logout</x-ui.button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  
  <x-slot:scripts>
  </x-slot:scripts>
  <x-admin.tutorial />

</x-layouts.app>
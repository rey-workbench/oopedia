<x-layouts.app title="Terima Kasih - UEQ Survey" theme="mahasiswa">
    <x-slot:styles>

    </x-slot:styles>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="ueq-thankyou-card">
                    <div class="ueq-thankyou-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="ueq-thankyou-title">Terima Kasih!</h2>
                    <p class="ueq-thankyou-message">
                        Kami sangat menghargai waktu dan masukan yang Anda berikan melalui survey UEQ ini. 
                        Feedback Anda sangat berharga untuk pengembangan aplikasi OOPEDIA ke depannya.
                    </p>
                    <div class="ueq-thankyou-decoration">
                        <div class="ueq-decoration-item"></div>
                        <div class="ueq-decoration-item"></div>
                        <div class="ueq-decoration-item"></div>
                    </div>
                    <div class="ueq-thankyou-actions">
                        <x-ui.button href="{{ route('mahasiswa.dashboard') }}" variant="primary">
                            <i class="fas fa-home me-2"></i>Kembali ke Dashboard
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
export class ErrorState {
    status = $state(404);

    title = $derived(this.status === 404 ? "Halaman Tidak Ditemukan" : this.status === 403 ? "Akses Ditolak" : "Terjadi Kesalahan");

    message = $derived(
        this.status === 404
            ? "Maaf, halaman yang Anda cari tidak dapat ditemukan."
            : this.status === 403
                ? "Maaf, Anda tidak memiliki izin untuk mengakses halaman ini."
                : "Terjadi kesalahan yang tidak terduga."
    );

    constructor(status: any) {
        this.status = status;
    }
}

import { BaseState } from '@/states/BaseState.svelte';

export class ErrorState extends BaseState {
    status = $state(404);

    title = $derived(this.getTitleByStatus(this.status));
    message = $derived(this.getMessageByStatus(this.status));

    constructor(status: number) {
        super();
        this.hydrate({ status });
    }

    private getTitleByStatus(status: number): string {
        switch (status) {
            case 404:
                return 'Halaman Tidak Ditemukan';
            case 403:
                return 'Akses Ditolak';
            default:
                return 'Terjadi Kesalahan';
        }
    }

    private getMessageByStatus(status: number): string {
        switch (status) {
            case 404:
                return 'Maaf, halaman yang Anda cari tidak dapat ditemukan.';
            case 403:
                return 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.';
            default:
                return 'Terjadi kesalahan yang tidak terduga.';
        }
    }
}

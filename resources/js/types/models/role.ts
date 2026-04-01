export interface Role {
    id: string;
    role_name: 'superadmin' | 'dosen' | 'mahasiswa' | 'guest';
    created_at?: string;
    updated_at?: string;
}

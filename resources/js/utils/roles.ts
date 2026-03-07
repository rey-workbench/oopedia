/**
 * Role constants and helpers.
 * Single source of truth for role IDs — no more magic numbers scattered across the codebase.
 */

export const ROLE = {
    SUPERADMIN: 'superadmin',
    ADMIN: 'dosen',
    MAHASISWA: 'mahasiswa',
    PENDING: 'pending',
} as const;

export type RoleName = (typeof ROLE)[keyof typeof ROLE];

/**
 * Returns true if the given role_name belongs to an admin (superadmin or admin).
 */
export function isAdmin(roleName: string | null | undefined): boolean {
    return roleName === ROLE.SUPERADMIN || roleName === ROLE.ADMIN;
}

/**
 * Returns true if the given role_name belongs to a student (active or pending).
 */
export function isStudent(roleName: string | null | undefined): boolean {
    return roleName === ROLE.MAHASISWA || roleName === ROLE.PENDING;
}

/**
 * Returns true if the given role_name is superadmin only.
 */
export function isSuperAdmin(roleName: string | null | undefined): boolean {
    return roleName === ROLE.SUPERADMIN;
}

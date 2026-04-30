/**
 * Role constants and helpers.
 * Single source of truth for role IDs — no more magic numbers scattered across the codebase.
 */

export const ROLE = {
    SUPERADMIN: 'superadmin',
    DOSEN: 'dosen',
    MAHASISWA: 'mahasiswa',
    GUEST: 'guest',
} as const;

export type RoleName = (typeof ROLE)[keyof typeof ROLE];

/**
 * Returns true if the given role_name belongs to an admin (superadmin or dosen).
 */
export function isAdmin(roleName: string | null | undefined): boolean {
    return roleName === ROLE.SUPERADMIN || roleName === ROLE.DOSEN;
}

/**
 * Returns true if the given role_name belongs to a student.
 */
export function isStudent(roleName: string | null | undefined): boolean {
    return roleName === ROLE.MAHASISWA;
}

/**
 * Returns true if the given role_name belongs to a guest.
 */
export function isGuest(roleName: string | null | undefined): boolean {
    return roleName === ROLE.GUEST;
}

/**
 * Returns true if the given role_name is superadmin only.
 */
export function isSuperAdmin(roleName: string | null | undefined): boolean {
    return roleName === ROLE.SUPERADMIN;
}

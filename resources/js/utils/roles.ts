/**
 * Role constants and helpers.
 * Single source of truth for role IDs — no more magic numbers scattered across the codebase.
 */

export const ROLE = {
    SUPERADMIN: 1,
    ADMIN: 2,
    MAHASISWA: 3,
    PENDING: 4,
} as const;

export type RoleId = (typeof ROLE)[keyof typeof ROLE];

/**
 * Returns true if the given role_id belongs to an admin (superadmin or admin).
 */
export function isAdmin(roleId: number | null | undefined): boolean {
    return roleId === ROLE.SUPERADMIN || roleId === ROLE.ADMIN;
}

/**
 * Returns true if the given role_id belongs to a student (active or pending).
 */
export function isStudent(roleId: number | null | undefined): boolean {
    return roleId === ROLE.MAHASISWA || roleId === ROLE.PENDING;
}

/**
 * Returns true if the given role_id is superadmin only.
 */
export function isSuperAdmin(roleId: number | null | undefined): boolean {
    return roleId === ROLE.SUPERADMIN;
}

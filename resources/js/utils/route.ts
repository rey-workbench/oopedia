/**
 * Route definition for OOPedia
 * Standardized routes to ensure consistency across the application.
 */
export const ROUTES = {
    ADMIN: {
        DASHBOARD: '/admin/dashboard',
        MATERIALS: {
            INDEX: '/admin/materials',
            CREATE: '/admin/materials/create',
            EDIT: (id: string | number) => `/admin/materials/${id}/edit`,
            SHOW: (id: string | number) => `/admin/materials/${id}`,
            UPDATE: (id: string | number) => `/admin/materials/${id}`,
            DELETE: (id: string | number) => `/admin/materials/${id}`,
            JSON: (id: string | number) => `/admin/materials/${id}/json`,
            SUBMATERIALS: {
                INDEX: (id: string | number) => `/admin/materials/${id}/submaterials`,
                CREATE: (id: string | number) => `/admin/materials/${id}/submaterials/create`,
                EDIT: (matId: string | number, subId: string | number) => `/admin/materials/${matId}/submaterials/${subId}/edit`,
                JSON: (id: string | number) => `/admin/materials/${id}/submaterials/json`,
            }
        },
        QUESTIONS: {
            INDEX: '/admin/questions',
            CREATE: '/admin/questions/create',
            EDIT: (id: string | number) => `/admin/questions/${id}/edit`,
            UPDATE: (id: string | number) => `/admin/questions/${id}`,
            DELETE: (id: string | number) => `/admin/questions/${id}`,
        },
        STUDENTS: {
            INDEX: '/admin/students',
            IMPORT: '/admin/students/import',
            SHOW: (id: string | number) => `/admin/students/${id}`,
            DELETE: (id: string | number) => `/admin/students/${id}`,
            PROGRESS: (id: string | number) => `/admin/students/${id}/progress`,
        },
        USERS: {
            INDEX: '/admin/users',
            CREATE: '/admin/users/create',
            EDIT: (id: string | number) => `/admin/users/${id}/edit`,
            DELETE: (id: string | number) => `/admin/users/${id}`,
            IMPORT: '/admin/users/import',
            PENDING: '/admin/users/pending',
        },
        UEQ: {
            INDEX: '/admin/ueq-survey',
            SHOW: (id: string | number) => `/admin/ueq-survey/${id}`,
        }
    },
    MAHASISWA: {
        DASHBOARD: '/mahasiswa/dashboard',
        MATERIALS: {
            INDEX: '/mahasiswa/materials',
            SHOW: (id: string | number) => `/mahasiswa/materials/${id}`,
            RESET: (id: string | number) => `/mahasiswa/materials/${id}/reset`,
            QUESTIONS: {
                CATALOG: '/mahasiswa/materials/questions',
                SHOW: (id: string | number) => `/mahasiswa/materials/${id}/questions`,
                LEVELS: (id: string | number) => `/mahasiswa/materials/${id}/questions/levels`,
                REVIEW: (id: string | number) => `/mahasiswa/materials/${id}/questions/review`,
                CHECK: (matId: string | number, quesId: string | number) => `/mahasiswa/materials/${matId}/questions/${quesId}/check`,
                ATTEMPTS: (matId: string | number, quesId: string | number) => `/mahasiswa/materials/${matId}/questions/${quesId}/attempts`,
            }
        },
        SUBMATERIALS: {
            SHOW: (matId: string | number, subId: string | number) => `/mahasiswa/materials/${matId}/submaterials/${subId}`,
        },
        LEADERBOARD: '/mahasiswa/leaderboard',
        PROFILE: '/mahasiswa/profile',
        UEQ: {
            CREATE: '/mahasiswa/ueq/create',
            THANK_YOU: '/mahasiswa/ueq/thank-you',
        }
    },
    AUTH: {
        LOGIN: '/login',
        REGISTER: '/register',
        LOGOUT: '/logout',
    }
};

/**
 * Route Helper to handle navigation standardized
 */
import { router } from "@inertiajs/svelte";

export const route = {
    visit: (url: string, options = {}) => router.visit(url, options),
    post: (url: string, data = {}, options = {}) => router.post(url, data, options),
    put: (url: string, data = {}, options = {}) => router.put(url, data, options),
    delete: (url: string, options = {}) => router.delete(url, options),
    reload: (options = {}) => router.reload(options),
};

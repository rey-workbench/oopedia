/**
 * Route definition for OOPedia
 * Standardized routes to ensure consistency across the application.
 */
export const ROUTES = {
    HOME: '/',
    ADMIN: {
        DASHBOARD: '/admin/dashboard',
        PROFILE: '/admin/profile',
        MATERIALS: {
            INDEX: '/admin/materials',
            CREATE: '/admin/materials/create',
            EDIT: (id: string | number) => `/admin/materials/${id}/edit`,
            SHOW: (id: string | number) => `/admin/materials/${id}`,
            UPDATE: (id: string | number) => `/admin/materials/${id}`,
            DELETE: (id: string | number) => `/admin/materials/${id}`,
            JSON: (id: string | number) => `/admin/materials/${id}/json`,
            QUESTIONS: {
                INDEX: (id: string | number) => `/admin/materials/${id}/questions`,
                CREATE: (id: string | number) => `/admin/materials/${id}/questions/create`,
            },
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
            APPROVE: (id: string | number) => `/admin/users/${id}/approve`,
            REJECT: (id: string | number) => `/admin/users/${id}/reject`,
        },
        PENDING_ADMINS: {
            INDEX: '/admin/pending-admins',
        },
        UEQ: {
            INDEX: '/admin/surveys/ueq',
            SHOW: (id: string | number) => `/admin/surveys/ueq/${id}`,
            EXPORT: '/admin/surveys/ueq/export',
        },
        MSLQ: {
            INDEX: '/admin/surveys/mslq',
            SHOW: (id: string | number) => `/admin/surveys/mslq/${id}`,
            EXPORT: '/admin/surveys/mslq/export',
        },
        SUS: {
            INDEX: '/admin/surveys/sus',
            SHOW: (id: string | number) => `/admin/surveys/sus/${id}`,
            EXPORT: '/admin/surveys/sus/export',
        },
        ADAPTIVE_RULES: {
            INDEX: '/admin/adaptive-rules',
            CREATE: '/admin/adaptive-rules/create',
            STORE: '/admin/adaptive-rules',
            EDIT: (id: string | number) => `/admin/adaptive-rules/${id}/edit`,
            UPDATE: (id: string | number) => `/admin/adaptive-rules/${id}`,
            DELETE: (id: string | number) => `/admin/adaptive-rules/${id}`,
        },
        ADAPTIVE_ACTIONS: {
            INDEX: '/admin/adaptive-actions',
            STORE: '/admin/adaptive-actions',
            UPDATE: (id: string | number) => `/admin/adaptive-actions/${id}`,
            DELETE: (id: string | number) => `/admin/adaptive-actions/${id}`,
        },
    },
    MAHASISWA: {
        DASHBOARD: '/mahasiswa/dashboard',
        IN_PROGRESS: '/mahasiswa/dashboard/in-progress',
        COMPLETED: '/mahasiswa/dashboard/completed',
        MATERIALS: {
            INDEX: '/mahasiswa/materials',
            SHOW: (id: string | number) => `/mahasiswa/materials/${id}`,
            RESET: (id: string | number) => `/mahasiswa/materials/${id}/reset`,
            QUESTIONS: {
                CATALOG: '/mahasiswa/materials/questions',
                SHOW: (id: string | number, subId?: string | number) =>
                    subId
                        ? `/mahasiswa/materials/${id}/questions/${subId}`
                        : `/mahasiswa/materials/${id}/questions`,
                LEVELS: (id: string | number) => `/mahasiswa/materials/${id}/questions/levels`,
                REVIEW: (id: string | number, difficulty?: string) =>
                    difficulty
                        ? `/mahasiswa/materials/${id}/questions/review/${difficulty}`
                        : `/mahasiswa/materials/${id}/questions/review`,
                CHECK: (matId: string | number, quesId: string | number) =>
                    `/mahasiswa/materials/${matId}/questions/${quesId}/check`,
                ATTEMPTS: (matId: string | number, quesId: string | number) =>
                    `/mahasiswa/materials/${matId}/questions/${quesId}/attempts`,
                HINT: (matId: string | number, quesId: string | number) =>
                    `/mahasiswa/materials/${matId}/questions/${quesId}/hint`,
            },
        },
        LEADERBOARD: '/mahasiswa/leaderboard',
        PROFILE: '/mahasiswa/profile',
        CERTIFICATES: {
            INDEX: '/mahasiswa/certificates',
            DOWNLOAD: (id: string | number) => `/mahasiswa/certificates/${id}/download`,
            PREVIEW: (id: string | number, userId?: string | number) =>
                userId
                    ? `/mahasiswa/certificates/preview/${id}/${userId}`
                    : `/mahasiswa/certificates/preview/${id}`,
        },
        UEQ: {
            CREATE: '/mahasiswa/surveys/ueq/create',
            THANK_YOU: '/mahasiswa/surveys/ueq/thank-you',
        },
        MSLQ: {
            INDEX: '/mahasiswa/surveys/mslq',
            CREATE: '/mahasiswa/surveys/mslq/create',
            STORE: '/mahasiswa/surveys/mslq',
            THANK_YOU: '/mahasiswa/surveys/mslq/thankyou',
        },
        SUS: {
            CREATE: '/mahasiswa/surveys/sus/create',
            THANK_YOU: '/mahasiswa/surveys/sus/thank-you',
        },
    },
    AUTH: {
        LOGIN: '/login',
        REGISTER: '/register',
        LOGOUT: '/logout',
        FORGOT_PASSWORD: '/forgot-password',
        RESET_PASSWORD: '/reset-password',
        GOOGLE: '/auth/google',
        GOOGLE_CALLBACK: '/auth/google/callback',
        GOOGLE_CHOOSE_ROLE: '/auth/google/choose-role',
        GOOGLE_REGISTER: (role: string) => `/auth/google/register/${role}`,
    },
} as const;

export type RouteKeys = keyof typeof ROUTES;

/**
 * resources/js/types/api/ApiDomain.ts
 *
 * Generic Response contracts and shared API shapes.
 */

export interface ApiResponse<T = unknown> {
    success: boolean;
    data: T;
    message?: string;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

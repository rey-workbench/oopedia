/**
 * Format an ISO date string using the Indonesian locale.
 *
 * @param dateString - ISO date string (e.g. "2025-01-15T10:30:00Z")
 * @param options    - Intl.DateTimeFormat options (merged with defaults)
 * @returns Formatted date string, or empty string if input is falsy
 */
export function formatDate(
    dateString: string | null | undefined,
    options: Intl.DateTimeFormatOptions = {}
): string {
    if (!dateString) return '';

    const defaults: Intl.DateTimeFormatOptions = {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    };

    return new Date(dateString).toLocaleDateString('id-ID', {
        ...defaults,
        ...options,
    });
}

/**
 * Return a human-readable relative time string in Indonesian.
 *
 * Examples: "5 detik yang lalu", "3 menit yang lalu", "2 jam yang lalu", "1 hari yang lalu"
 *
 * @param isoDate - ISO date string
 * @returns Relative time string, or empty string if input is falsy
 */
export function relativeTime(isoDate: string | null | undefined): string {
    if (!isoDate) return '';

    const date = new Date(isoDate);
    const now = new Date();
    const diff = Math.floor((now.getTime() - date.getTime()) / 1000); // seconds

    if (diff < 60) return `${diff} detik yang lalu`;

    const min = Math.floor(diff / 60);
    if (min < 60) return `${min} menit yang lalu`;

    const hour = Math.floor(min / 60);
    if (hour < 24) return `${hour} jam yang lalu`;

    const day = Math.floor(hour / 24);
    return `${day} hari yang lalu`;
}

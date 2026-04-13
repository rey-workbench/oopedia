let idCounter = 0;

export function generateId(prefix = 'id'): string {
    return `${prefix}-${++idCounter}`;
}

export function generateStableId(prefix: string): string {
    const stored = typeof window !== 'undefined' ? (window as any).__idRegistry?.[prefix] : null;
    if (stored) return stored;

    const id = `${prefix}-${Math.random().toString(36).slice(2, 11)}`;
    if (typeof window !== 'undefined') {
        (window as any).__idRegistry = (window as any).__idRegistry || {};
        (window as any).__idRegistry[prefix] = id;
    }
    return id;
}

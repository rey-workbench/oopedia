import { Code, BookOpen } from 'lucide-svelte';
import type { ComponentType } from 'svelte';

type ContentType = string; // "sintaks" | "teori" — kept loose for flexibility

/**
 * Gradient background for content-type headers.
 */
export function getGradientClass(type: ContentType): string {
    return type === 'sintaks' ? 'bg-emerald-600' : 'bg-primary-600';
}

/**
 * Text color for content-type labels.
 */
export function getTextClass(type: ContentType): string {
    return type === 'sintaks' ? 'text-emerald-600' : 'text-primary-600';
}

/**
 * Solid background color for badges / pills.
 */
export function getBgClass(type: ContentType): string {
    return type === 'sintaks' ? 'bg-emerald-600' : 'bg-primary-600';
}

/**
 * Shadow color for cards.
 */
export function getShadowClass(type: ContentType): string {
    return type === 'sintaks' ? 'shadow-emerald-900/20' : 'shadow-primary-900/20';
}

/**
 * Icon component for the content type.
 */
export function getIcon(type: ContentType): ComponentType {
    return type === 'sintaks' ? Code : BookOpen;
}

/**
 * Badge label text.
 */
export function getBadgeLabel(type: ContentType): string {
    return type === 'sintaks' ? 'Sintaks' : 'Teori';
}


import { Code, BookOpen } from 'lucide-svelte';
import type { ComponentType } from 'svelte';

import type { ContentCategory } from '@/types';

/**
 * Gradient background for content-type headers.
 */
export function getGradientClass(type: ContentCategory | string): string {
    return type === 'sintaks' ? 'bg-emerald-600' : 'bg-primary-600';
}

/**
 * Text color for content-type labels.
 */
export function getTextClass(type: ContentCategory | string): string {
    return type === 'sintaks' ? 'text-emerald-600' : 'text-primary-600';
}

/**
 * Solid background color for badges / pills.
 */
export function getBgClass(type: ContentCategory | string): string {
    return type === 'sintaks' ? 'bg-emerald-600' : 'bg-primary-600';
}

/**
 * Shadow color for cards.
 */
export function getShadowClass(type: ContentCategory | string): string {
    return type === 'sintaks' ? 'shadow-emerald-900/20' : 'shadow-primary-900/20';
}

/**
 * Icon component for the content type.
 */
export function getIcon(type: ContentCategory | string): ComponentType {
    return type === 'sintaks' ? Code : BookOpen;
}

/**
 * Badge label text.
 */
export function getBadgeLabel(type: ContentCategory | string): string {
    return type === 'sintaks' ? 'Sintaks' : 'Teori';
}


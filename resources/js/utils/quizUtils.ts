/**
 * Quiz display utilities.
 * View-layer helpers extracted from state classes to respect
 * the Single Responsibility Principle.
 */
import type { DifficultyLevel } from '@/types/models';

const DIFFICULTY_LABELS: Record<DifficultyLevel, string> = {
    beginner: 'Pemula',
    medium: 'Menengah',
    hard: 'Sulit',
    final: 'Final',
};

const DIFFICULTY_COLORS: Record<DifficultyLevel, string> = {
    beginner: 'text-emerald-600 bg-emerald-50',
    medium: 'text-amber-600 bg-amber-50',
    hard: 'text-rose-600 bg-rose-50',
    final: 'text-violet-600 bg-violet-50',
};

export function getDifficultyLabel(diff: DifficultyLevel | string): string {
    return DIFFICULTY_LABELS[diff as DifficultyLevel] ?? diff;
}

export function getDifficultyColor(diff: DifficultyLevel | string): string {
    return DIFFICULTY_COLORS[diff as DifficultyLevel] ?? 'text-slate-600 bg-slate-50';
}

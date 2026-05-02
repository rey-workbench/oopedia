/**
 * Quiz display utilities.
 * View-layer helpers extracted from state classes to respect
 * the Single Responsibility Principle.
 */
import type { QuestionDifficulty } from '@/types';

const DIFFICULTY_LABELS: Record<QuestionDifficulty, string> = {
    beginner: 'Pemula',
    medium: 'Menengah',
    hard: 'Sulit',
    final: 'Final',
};

const DIFFICULTY_COLORS: Record<QuestionDifficulty, string> = {
    beginner: 'text-emerald-600 bg-emerald-50',
    medium: 'text-amber-600 bg-amber-50',
    hard: 'text-rose-600 bg-rose-50',
    final: 'text-violet-600 bg-violet-50',
};

export function getDifficultyLabel(diff: QuestionDifficulty | string): string {
    return DIFFICULTY_LABELS[diff as QuestionDifficulty] ?? diff;
}

export function getDifficultyColor(diff: QuestionDifficulty | string): string {
    return DIFFICULTY_COLORS[diff as QuestionDifficulty] ?? 'text-slate-600 bg-slate-50';
}

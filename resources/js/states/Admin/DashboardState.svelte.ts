import { BaseState } from '@/states/BaseState.svelte';
import type {
    AdminDashboardData,
    RecentProgressItem,
    StudentAnalytics,
    StudentProgressItem,
    StudentNeedingAttention,
    Material,
} from '@/types';

export class AdminDashboardState extends BaseState {
    total_students = $state(0);
    total_materials = $state(0);
    total_questions = $state(0);
    active_students = $state(0);
    recent_progress = $state<RecentProgressItem[]>([]);
    student_progress = $state<StudentProgressItem[]>([]);
    popular_materials = $state<Material[]>([]);
    student_analytics = $state<StudentAnalytics>({
        distribution: {},
        module_performance: { labels: [], data: [] },
    });
    material_stats = $state<Material[]>([]);
    students_needing_attention = $state<StudentNeedingAttention[]>([]);

    constructor(data?: AdminDashboardData) {
        super();
        this.hydrate(data as unknown as Record<string, unknown>);
    }
}

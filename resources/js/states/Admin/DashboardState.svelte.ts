import { BaseState } from '@/states/BaseState.svelte';
import type {
    AdminDashboardData,
    RecentProgressItem,
    StudentAnalytics,
    StudentProgressItem,
    PopularMaterialItem,
    MaterialStatsItem,
    StudentNeedingAttention,
} from '@/types';

export class AdminDashboardState extends BaseState {
    total_students = $state(0);
    total_materials = $state(0);
    total_questions = $state(0);
    active_students = $state(0);
    recent_progress = $state<RecentProgressItem[]>([]);
    student_progress = $state<StudentProgressItem[]>([]);
    popular_materials = $state<PopularMaterialItem[]>([]);
    student_analytics = $state<StudentAnalytics>({ distribution: {}, radar: {} });
    material_stats = $state<MaterialStatsItem[]>([]);
    students_needing_attention = $state<StudentNeedingAttention[]>([]);

    constructor(data?: AdminDashboardData) {
        super();
        this.hydrate(data as any);
    }
}

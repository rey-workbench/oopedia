import { registerAdminTutorials } from './admin';
import { registerMahasiswaTutorials } from './mahasiswa';
import { registerGlobalTutorials } from './global';
import { registerAuthTutorials } from './auth';

export {
    registerAdminTutorials,
    registerMahasiswaTutorials,
    registerGlobalTutorials,
    registerAuthTutorials,
};

export function initTutorials() {
    registerAdminTutorials();
    registerMahasiswaTutorials();
    registerAuthTutorials();
    registerGlobalTutorials();
}

/**
 * Get tour ID based on current URL and user role
 */
export function getTourIdFromUrl(url: string, isAdminRole: boolean): string {
    let tourId = isAdminRole ? 'admin_dashboard' : 'mahasiswa_dashboard';

    // Normalize path for matching
    const path = url.split('?')[0] || '';

    if (isAdminRole) {
        if (path.includes('/admin/materials')) {
            if (path.includes('/questions')) {
                if (path.includes('/create') || path.includes('/edit'))
                    return 'admin_question_editor';
                return 'admin_questions';
            }
            if (path.includes('/create') || path.includes('/edit')) return 'admin_material_editor';
            return 'admin_materials';
        }

        if (path.includes('/admin/questions')) {
            if (path.includes('/create') || path.includes('/edit')) return 'admin_question_editor';
            return 'admin_questions';
        }

        if (path.includes('/admin/students')) {
            if (path.includes('/import')) return 'admin_student_import';
            if (path.includes('/progress')) return 'admin_student_progress';
            return 'admin_students';
        }

        if (path.includes('/admin/users')) {
            if (path.includes('/create') || path.includes('/edit')) return 'admin_user_editor';
            if (path.includes('/pending')) return 'admin_pending_admins';
            return 'admin_users';
        }

        if (path.includes('/admin/pending-admins')) return 'admin_pending_admins';

        if (path.includes('/admin/surveys/ueq')) {
            // Check if it's a detail page (ends with ID or has export)
            const parts = path.split('/');
            const lastPart = parts[parts.length - 1];
            if (lastPart && lastPart !== 'ueq-survey' && lastPart !== 'export') {
                return 'admin_ueq_detail';
            }
            return 'admin_ueq';
        }

        if (path.includes('/admin/surveys/mslq')) {
            // Check if it's a detail page (ends with ID)
            const parts = path.split('/');
            const lastPart = parts[parts.length - 1];
            if (lastPart && lastPart !== 'mslq') {
                return 'admin_mslq_detail';
            }
            return 'admin_mslq';
        }

        if (path.includes('/admin/adaptive-rules')) {
            if (path.includes('/create') || path.includes('/edit'))
                return 'admin_adaptive_rule_editor';
            return 'admin_adaptive_rules';
        }

        if (path.includes('/admin/dashboard')) return 'admin_dashboard';
    } else {
        if (path.includes('/mahasiswa/dashboard/in-progress'))
            return 'mahasiswa_dashboard_inprogress';
        if (path.includes('/mahasiswa/dashboard/completed')) return 'mahasiswa_dashboard_completed';
        if (path.includes('/mahasiswa/dashboard')) return 'mahasiswa_dashboard';

        if (path.includes('/mahasiswa/materials/') && path.includes('/questions')) {
            if (path.includes('/questions/levels')) return 'mahasiswa_quiz_levels';
            if (path.includes('/questions/review')) return 'mahasiswa_quiz_review';

            // Check if it's a session or index
            const parts = path.split('/questions');
            const matParts = parts[0]?.split('/materials/');
            if (matParts?.[1] && matParts[1].length > 0) return 'mahasiswa_quiz_session';
            return 'mahasiswa_quiz_index';
        }

        if (path.includes('/mahasiswa/materials/')) return 'mahasiswa_materials_show';
        if (path.includes('/mahasiswa/materials')) return 'mahasiswa_materials';

        if (path.includes('/mahasiswa/leaderboard')) return 'mahasiswa_leaderboard';
        if (path.includes('/mahasiswa/certificates')) return 'mahasiswa_certificates';

        if (path.includes('/mahasiswa/surveys/ueq')) {
            if (path.includes('/thank-you') || path.includes('/thankyou'))
                return 'mahasiswa_ueq_thankyou';
            return 'mahasiswa_ueq';
        }

        if (path.includes('/mahasiswa/profile')) return 'mahasiswa_profile';

        if (path.includes('/mahasiswa/surveys/mslq')) return 'mahasiswa_mslq';
    }

    return tourId;
}

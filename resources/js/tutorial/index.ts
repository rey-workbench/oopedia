import { registerAdminTutorials } from './admin';
import { registerMahasiswaTutorials } from './mahasiswa';
import { registerGlobalTutorials } from './global';
import { registerAuthTutorials } from './auth';
import { registerLandingTutorials } from './landing';

export {
    registerAdminTutorials,
    registerMahasiswaTutorials,
    registerGlobalTutorials,
    registerAuthTutorials,
    registerLandingTutorials,
};

export function initTutorials() {
    registerAdminTutorials();
    registerMahasiswaTutorials();
    registerAuthTutorials();
    registerLandingTutorials();
    registerGlobalTutorials();
}

/**
 * Get tour ID based on current URL and user role
 */
export function getTourIdFromUrl(url: string, isAdminRole: boolean): string {
    let tourId = isAdminRole ? 'admin_dashboard' : 'mahasiswa_dashboard';
    if (isAdminRole) {
        if (url.includes('/admin/materials')) tourId = 'admin_materials';
        else if (url.includes('/admin/students')) tourId = 'admin_students';
        else if (url.includes('/admin/users')) tourId = 'admin_users';
        else if (url.includes('/admin/ueq-survey')) tourId = 'admin_ueq';
        else if (url.includes('/admin/questions')) tourId = 'admin_questions';
    } else {
        if (url.includes('/mahasiswa/dashboard/in-progress'))
            tourId = 'mahasiswa_dashboard_inprogress';
        else if (url.includes('/mahasiswa/dashboard/completed'))
            tourId = 'mahasiswa_dashboard_completed';
        else if (url.includes('/mahasiswa/dashboard')) tourId = 'mahasiswa_dashboard';
        else if (url.includes('/submaterials')) tourId = 'mahasiswa_submaterials_show';
        else if (url.includes('/mahasiswa/materials/questions')) {
            if (url.includes('/review')) tourId = 'mahasiswa_quiz_review';
            else tourId = 'mahasiswa_quiz_index';
        } else if (url.includes('/mahasiswa/materials/') && url.includes('/questions/levels'))
            tourId = 'mahasiswa_quiz_levels';
        else if (url.includes('/mahasiswa/materials/') && url.includes('/questions/'))
            tourId = 'mahasiswa_quiz_session';
        else if (url.includes('/mahasiswa/materials/')) tourId = 'mahasiswa_materials_show';
        else if (url.includes('/mahasiswa/materials')) tourId = 'mahasiswa_materials';
        else if (url.includes('/mahasiswa/leaderboard')) tourId = 'mahasiswa_leaderboard';
        else if (url.includes('/mahasiswa/certificates')) tourId = 'mahasiswa_certificates';
        else if (url.includes('/mahasiswa/ueq-survey')) {
            if (url.includes('/thank-you') || url.includes('/thankyou'))
                tourId = 'mahasiswa_ueq_thankyou';
            else tourId = 'mahasiswa_ueq';
        } else if (url.includes('/mahasiswa/profile')) tourId = 'mahasiswa_profile';
    }
    return tourId;
}

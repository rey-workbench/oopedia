export { ROUTES } from './route';
export type { RouteKeys } from './route';
export { route, navigateTo, redirectTo } from './router';
export { confirmDelete } from './confirmDelete';
export { generateId, generateStableId } from './ids';
export { formatDate, relativeTime } from './formatters';
export { handleImagePreview } from './imagePreview';
export { getDifficultyLabel, getDifficultyColor } from './quizUtils';
export { isAdmin, isSuperAdmin, isStudent, ROLE } from './roles';
export type { RoleName } from './roles';
export {
    activateExamProtection,
    deactivateExamProtection,
    isProtectionActive,
    isDebugMode,
} from './examProtection';
export type { ViolationType } from './examProtection';
export { playSound } from './audio';

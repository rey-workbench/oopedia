import { driver, type Driver, type DriveStep } from 'driver.js';
import 'driver.js/dist/driver.css';
import { BaseState } from '../BaseState.svelte';

export interface TutorialRegistration {
    tourId: string;
    steps: DriveStep[];
    group?: 'global' | 'page';
    priority?: number;
}

/**
 * Tutorial State - Svelte 5 native state
 * Manages onboarding tutorials across the application using a registry pattern.
 */
class TutorialState extends BaseState {
    hasSeenTutorial = $state<Record<string, boolean>>({});
    private registry = $state<TutorialRegistration[]>([]);
    private driverObj: Driver | null = null;

    constructor() {
        super();
        this.loadState();
    }

    /**
     * Register steps for a specific tour or global navigation
     */
    registerSteps(registration: TutorialRegistration) {
        // Prevent duplicate registrations for the same steps
        const exists = this.registry.some(
            (r) =>
                r.tourId === registration.tourId &&
                JSON.stringify(r.steps) === JSON.stringify(registration.steps)
        );

        if (!exists) {
            this.registry.push({
                group: 'page',
                priority: 10,
                ...registration,
            });
        }
    }

    /**
     * Get all steps for a specific tour, including global steps
     */
    private getStepsForTour(tourId: string, includeGlobal: boolean = true): DriveStep[] {
        const pageSteps = this.registry
            .filter((r) => r.tourId === tourId && r.group !== 'global')
            .sort((a, b) => (a.priority || 0) - (b.priority || 0))
            .flatMap((r) => r.steps);

        const globalSteps = includeGlobal 
            ? this.registry
                .filter((r) => r.group === 'global')
                .sort((a, b) => (a.priority || 0) - (b.priority || 0))
                .flatMap((r) => r.steps)
            : [];

        // Combine global steps (sidebar & navbar) with the specific page steps
        return [...globalSteps, ...pageSteps];
    }

    /**
     * Load seen tutorials from localStorage
     */
    private loadState() {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('oopedia_tutorials');
            if (saved) {
                try {
                    this.hasSeenTutorial = JSON.parse(saved);
                } catch (e) {
                    console.error('Failed to parse tutorial state', e);
                    this.hasSeenTutorial = {};
                }
            }
        }
    }

    /**
     * Save seen tutorials to localStorage
     */
    private saveState() {
        if (typeof window !== 'undefined') {
            localStorage.setItem('oopedia_tutorials', JSON.stringify(this.hasSeenTutorial));
        }
    }

    /**
     * Start a specific tour by ID
     */
    startTour(tourId: string, force = false, includeGlobal = true) {
        if (typeof window === 'undefined') return;

        if (!force && this.hasSeenTutorial[tourId]) {
            return;
        }

        const steps = this.getStepsForTour(tourId, includeGlobal);

        if (steps.length === 0) {
            console.warn(`[Tutorial] No steps registered for tour ID "${tourId}".`);
            return;
        }

        // Filter out steps where elements are missing from the DOM to handle layout variations
        const validSteps = steps.filter((step) => {
            if (typeof step.element === 'string') {
                try {
                    return !!document.querySelector(step.element);
                } catch (e) {
                    console.warn(`[Tutorial] Invalid selector encountered: "${step.element}"`, e);
                    return false;
                }
            }
            return true;
        });

        if (validSteps.length === 0) {
            console.warn(
                `[Tutorial] Aborting tour '${tourId}': No registered elements were found in the current layout.`
            );
            return;
        }

        this.driverObj = driver({
            showProgress: true,
            animate: true,
            allowKeyboardControl: true,
            allowClose: true,
            stagePadding: 8,
            stageRadius: 20,
            popoverClass: 'oopedia-driver-popover',
            nextBtnText: 'Lanjut &rarr;',
            prevBtnText: '&larr; Kembali',
            doneBtnText: 'Selesai ✓',
            progressText: 'Langkah {{current}} dari {{total}}',
            steps: validSteps,
            onDestroyed: () => {
                // Mark as seen
                this.hasSeenTutorial[tourId] = true;
                this.saveState();
            },
        });

        this.driverObj.drive();
    }

    /**
     * Reset all tutorial progress
     */
    resetProgress() {
        this.hasSeenTutorial = {};
        this.saveState();
    }
}

export const tutorialState = new TutorialState();

/**
 * Helper to start a tutorial from components
 */
export function startTutorial(tourId: string, force = false, includeGlobal = true) {
    tutorialState.startTour(tourId, force, includeGlobal);
}

/**
 * Helper to reset progress
 */
export function resetTutorialProgress() {
    tutorialState.resetProgress();
}

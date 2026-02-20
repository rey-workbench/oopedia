import { router } from "@inertiajs/svelte";

export class UeqListState {
    surveys = $state<any[]>([]);
    averages = $state<any>({});
    classes = $state<any[]>([]);
    activeClass = $state("");

    constructor(surveys: any, averages: any, classes: any, activeClass: any) {
        this.surveys = surveys;
        this.averages = averages;
        this.classes = classes;
        this.activeClass = activeClass;
    }

    handleFilterChange(e: any) {
        router.get(
            "/admin/ueq-survey",
            { class: e.target.value },
            { preserveState: true, replace: true }
        );
    }

    exportResults() {
        const url = this.activeClass
            ? `/admin/ueq-survey/export?class=${this.activeClass}`
            : "/admin/ueq-survey/export";
        window.location.href = url;
    }
}

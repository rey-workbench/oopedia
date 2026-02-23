import { BaseState } from "@/states/BaseState.svelte";
import type { LearningProfile } from "@/types";

export class ProfileState extends BaseState {
    personalization = $state<LearningProfile | null>(null);

    constructor(personalization: LearningProfile) {
        super();
        this.personalization = personalization;
    }
}

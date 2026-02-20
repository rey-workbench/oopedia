import { BaseState } from "@/states/BaseState.svelte";

export class ProfileState extends BaseState {
    personalization = $state<any>({});

    constructor(personalization: any) {
        super();
        this.personalization = personalization;
    }
}

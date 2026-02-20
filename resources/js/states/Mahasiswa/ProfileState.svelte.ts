export class ProfileState {
    user = $state<any>({});
    personalization = $state<any>({});

    constructor(user: any, personalization: any) {
        this.user = user;
        this.personalization = personalization;
    }
}

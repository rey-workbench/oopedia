import { FormState } from '@/states/FormState.svelte';
import type { User, LearningPersonalization, ProfileForm } from '@/types';
import { ROUTES } from '@/utils/route';

export class ProfileState extends FormState<ProfileForm> {
    profileUser = $state<User | null>(null);
    personalization = $state<LearningPersonalization | null>(null);

    constructor(user: User | null, personalization: LearningPersonalization | null) {
        super({
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        });
        this.hydrate({ profileUser: user, personalization });

        if (this.profileUser) {
            this.form.name = this.profileUser.name ?? '';
            this.form.email = this.profileUser.email ?? '';
        }
    }

    submit() {
        this.submitForm('post', ROUTES.MAHASISWA.PROFILE.UPDATE, {
            _method: 'PUT',
            onSuccess: () => {
                this.form.password = '';
                this.form.password_confirmation = '';
            },
        });
    }
}

import { router } from '@inertiajs/svelte';
import { debounce } from 'lodash-es';
import { confirmDelete } from '@/utils/confirmDelete';
import { BaseState } from '@/states/BaseState.svelte';
import { FormState } from '@/states/FormState.svelte';
import { ROUTES } from '@/utils/route';
import type { User, Pagination } from '@/types';

/**
 * User List State
 */
export class UserListState extends BaseState {
    users = $state<Pagination<User>>({
        data: [],
        links: [],
        current_page: 1,
        from: null,
        last_page: 1,
        path: '',
        per_page: 10,
        to: null,
        first_page_url: '',
        last_page_url: '',
        next_page_url: null,
        prev_page_url: null,
        total: 0,
    });
    search = $state('');

    constructor(users: Pagination<User>, search: string) {
        super();
        this.hydrate({ users, search });
    }

    public handleSearch = debounce(() => {
        router.get(
            ROUTES.ADMIN.USERS.INDEX,
            { search: this.search },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    }, 300);

    public handleDelete(id: number) {
        confirmDelete(ROUTES.ADMIN.USERS.DELETE(id), 'Hapus pengguna ini?');
    }
}

/**
 * User Form State (Create/Edit)
 */
export class UserFormState extends FormState<{
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    role_id: number | '';
}> {
    targetUser = $state<User | null>(null);

    constructor(user: User | null) {
        super(UserFormState.prepareInitialValues(user), { isEdit: !!user });
        this.hydrate({ targetUser: user });
    }

    private static prepareInitialValues(user: User | null) {
        return {
            name: user?.name ?? '',
            email: user?.email ?? '',
            password: '',
            password_confirmation: '',
            role_id: user ? (user as any).role_id : '',
        };
    }

    public async submit() {
        const url = this.getSubmitUrl();
        await this.submitForm(this.isEdit ? 'put' : 'post', url);
    }

    private getSubmitUrl(): string {
        if (this.isEdit && this.targetUser) {
            // Remove /edit suffix if present in the route definition for update
            return ROUTES.ADMIN.USERS.EDIT(this.targetUser.id).replace('/edit', '');
        }
        return ROUTES.ADMIN.USERS.INDEX;
    }
}

/**
 * User Import State (Form)
 */
export class UserImportState extends FormState<{ excel_file: File | null }> {
    constructor() {
        super({
            excel_file: null,
        });
    }

    public async submit() {
        await this.submitForm('post', ROUTES.ADMIN.USERS.IMPORT);
    }

    public handleFileChange(e: Event) {
        const input = e.target as HTMLInputElement;
        this.form.excel_file = input.files?.[0] ?? null;
    }
}

/**
 * Pending Admin Approval State
 */
export class PendingAdminState extends BaseState {
    pendingAdmins = $state<User[]>([]);

    constructor(pendingAdmins: User[]) {
        super();
        this.hydrate({ pendingAdmins });
    }

    public handleApprove(id: number) {
        router.post(ROUTES.ADMIN.USERS.APPROVE(id));
    }

    public handleReject(id: number) {
        router.post(ROUTES.ADMIN.USERS.REJECT(id));
    }
}

/**
 * Pending Users State (for users awaiting approval)
 */
export class PendingUsersState extends BaseState {
    constructor() {
        super();
    }

    public logout() {
        router.post(ROUTES.AUTH.LOGOUT);
    }
}

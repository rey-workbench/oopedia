import { router } from '@inertiajs/svelte';
import { debounce } from 'lodash';
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
        total: 0,
    });
    search = $state('');

    constructor(users: Pagination<User>, search: string) {
        super();
        this.users = users;
        this.search = search;
    }

    handleSearch = debounce(() => {
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

    handleDelete(id: number) {
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
    role_id: string;
    gamification_level: string;
    xp: number;
}> {
    targetUser = $state<User | null>(null);

    constructor(user: User | null) {
        super(
            {
                name: user ? user.name : '',
                email: user ? user.email : '',
                password: '',
                password_confirmation: '',
                role_id: user ? user.role_id : '3',
                gamification_level: (user as any)?.gamification
                    ? (user as any).gamification.current_level
                    : 'Pemula',
                xp: (user as any)?.gamification ? (user as any).global_xp : 0,
            },
            { isEdit: !!user }
        );

        this.targetUser = user;
    }

    async submit() {
        const url = this.isEdit
            ? ROUTES.ADMIN.USERS.EDIT(this.targetUser!.id).replace('/edit', '')
            : ROUTES.ADMIN.USERS.INDEX;

        await this.submitForm(this.isEdit ? 'put' : 'post', url);
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

    async submit() {
        await this.submitForm('post', ROUTES.ADMIN.USERS.IMPORT);
    }

    handleFileChange(e: Event) {
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
        this.pendingAdmins = pendingAdmins;
    }

    handleApprove(id: number) {
        router.post(ROUTES.ADMIN.USERS.APPROVE(id));
    }

    handleReject(id: number) {
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

    logout() {
        router.post(ROUTES.AUTH.LOGOUT);
    }
}

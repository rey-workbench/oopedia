import { BaseState } from '@/states/BaseState.svelte';
import type {
    AdaptiveFact,
    AdaptiveAction,
    AdaptiveRuleForm,
    AdaptiveRuleFactItem,
    AdaptiveRuleDeductionItem,
} from '@/types';

/**
 * Adaptive Rule Editor State
 * Manages the complex drag-and-drop rule building interface for admins.
 */
export class AdaptiveRuleEditorState extends BaseState {
    // --- Data Repositories ---
    all_facts = $state<AdaptiveFact[]>([]);
    all_actions = $state<AdaptiveAction[]>([]);
    isEdit = $state(false);

    // --- UI Interactive State ---
    draggingSourceType = $state<string | null>(null);
    isDraggingOver = $state<string | null>(null);
    invalidDropZone = $state<string | null>(null);
    selectedMetadataKey = $state<string>('');

    // --- Constants & Config ---
    readonly CONDITION_KEYS = [
        { value: 'accuracy', label: 'Akurasi' },
        { value: 'hints_used', label: 'Bantuan' },
        { value: 'streak', label: 'Streak' },
        { value: 'level', label: 'Level' },
        { value: 'performance_metrics.trend', label: 'Tren' },
        { value: 'performance_metrics.speed', label: 'Kecepatan' },
        { value: 'performance_metrics.stagnant_count', label: 'Stagnan' },
        { value: 'current_session.hints', label: 'Bantuan Sesi' },
        { value: 'current_session.time_spent', label: 'Waktu Sesi' },
    ];

    readonly ALLOWED_VALUES: Record<string, Array<{ value: string; label: string }>> = {
        'performance_metrics.trend': [
            { value: 'up', label: 'Naik' },
            { value: 'stable', label: 'Stabil' },
            { value: 'down', label: 'Turun' },
        ],
        'performance_metrics.speed': [
            { value: 'fast', label: 'Cepat' },
            { value: 'normal', label: 'Normal' },
            { value: 'slow', label: 'Lambat' },
        ],
        level: [
            { value: 'Pemula', label: 'Pemula' },
            { value: 'Menengah', label: 'Menengah' },
            { value: 'Ahli', label: 'Ahli' },
        ],
    };

    readonly METADATA_KEYS = [
        { value: 'notify_teacher', label: 'Notify Teacher (Boolean)' },
        { value: 'notify_type', label: 'Notification Type (crisis/general)' },
        { value: 'show_motivation', label: 'Show Motivation (Boolean)' },
        { value: 'target_difficulty', label: 'Target Difficulty' },
        { value: 'forced_easy_count', label: 'Forced Easy Count' },
        { value: 'difficulty_steps', label: 'Difficulty Steps' },
        { value: 'gradual_scaffold_reduction', label: 'Scaffold Reduction (Boolean)' },
        { value: 'cross_topic_challenge', label: 'Cross Topic (Boolean)' },
        { value: 'unlock_advanced', label: 'Unlock Advanced (Boolean)' },
    ];

    constructor(data: {
        all_facts: AdaptiveFact[];
        all_actions: AdaptiveAction[];
        isEdit: boolean;
    }) {
        super();
        this.all_facts = data.all_facts;
        this.all_actions = data.all_actions;
        this.isEdit = data.isEdit;

        this.initializeDefaultMetadataKey();
    }

    private initializeDefaultMetadataKey() {
        const firstKey = this.METADATA_KEYS[0];
        if (firstKey) {
            this.selectedMetadataKey = firstKey.value;
        }
    }

    // --- ID Generation Logic ---

    public getNextAutoId(
        prefix: 'F' | 'V' | 'R',
        currentList: (AdaptiveRuleFactItem | AdaptiveRuleDeductionItem | string)[],
        dbList: (AdaptiveFact | AdaptiveAction)[] = []
    ): string {
        const pattern = new RegExp(`^${prefix}(\\d+)$`);

        const extractSequenceNumbers = (list: any[]) =>
            list
                .map((item) => {
                    const id = typeof item === 'string' ? item : item.id || item.key;
                    const match = id?.match(pattern);
                    return match ? parseInt(match[1]) : 0;
                })
                .filter((n) => !isNaN(n));

        const existingNumbers = [
            ...extractSequenceNumbers(currentList),
            ...extractSequenceNumbers(dbList),
        ];
        const maxNum = existingNumbers.length > 0 ? Math.max(...existingNumbers) : 0;

        return `${prefix}${String(maxNum + 1).padStart(2, '0')}`;
    }

    // --- Rule Building Methods ---

    public addCondition(form: AdaptiveRuleForm, factId = '') {
        const finalId = factId || this.getNextAutoId('F', form.facts, this.all_facts);
        const existingFact = this.all_facts?.find((f) => f.id === finalId);

        const logic = this.parseFactLogic(existingFact);

        form.facts = [
            ...form.facts,
            {
                id: finalId,
                key: logic.key || finalId,
                name: existingFact?.name || '',
                operator: logic.op || '==',
                value: logic.val !== undefined ? logic.val : 1,
                isManual: !factId,
            },
        ];
    }

    private parseFactLogic(fact?: AdaptiveFact): { op?: string; val?: any; key?: string } {
        if (!fact?.logic) return {};

        try {
            return typeof fact.logic === 'string' ? JSON.parse(fact.logic) : fact.logic;
        } catch {
            return {};
        }
    }

    public addDiagnosis(form: AdaptiveRuleForm, id = '') {
        const finalId = id || this.getNextAutoId('V', form.deduced_facts, this.all_facts);
        const existing = this.all_facts?.find((f) => f.id === finalId);

        const alreadyExists = form.deduced_facts.some((f) => f.id === finalId);
        if (alreadyExists) return;

        form.deduced_facts = [
            ...form.deduced_facts,
            {
                id: finalId,
                name: existing?.name || 'New Virtual Fact',
                isManual: !id,
            },
        ];
    }

    public addAction(form: AdaptiveRuleForm, id: string) {
        const alreadyExists = form.actions.some((a) => a.id === id);
        if (alreadyExists) {
            this.triggerInvalidDropFeedback('action');
            return;
        }

        form.actions = [...form.actions, { id, metadata: {} }];
    }

    private triggerInvalidDropFeedback(zone: string) {
        this.invalidDropZone = zone;
        setTimeout(() => (this.invalidDropZone = null), 400);
    }

    public addActionMetadata(form: AdaptiveRuleForm, index: number) {
        const action = form.actions[index];
        if (!action) return;

        const key = this.selectedMetadataKey;
        if (!key) return;

        if (action.metadata[key] !== undefined) {
            alert('Parameter ini sudah ada untuk aksi ini.');
            return;
        }

        action.metadata[key] = '';
    }

    public removeActionMetadata(form: AdaptiveRuleForm, index: number, key: string) {
        const action = form.actions[index];
        if (!action) return;

        const { [key]: _removed, ...remainingMetadata } = action.metadata;
        action.metadata = remainingMetadata;
    }

    // --- Drag and Drop Handling ---

    public handleDragStart(e: DragEvent, id: string, type: string) {
        if (!e.dataTransfer) return;

        const dragData = JSON.stringify({ id, type });
        e.dataTransfer.setData('application/json', dragData);
        e.dataTransfer.effectAllowed = 'copy';
        this.draggingSourceType = type;
    }

    public handleDrop(
        e: DragEvent,
        zone: 'condition' | 'deduction' | 'action',
        form: AdaptiveRuleForm
    ) {
        e.preventDefault();
        this.resetDragState();

        if (!e.dataTransfer) return;

        try {
            const rawData = e.dataTransfer.getData('application/json');
            if (!rawData) return;

            const { id, type } = JSON.parse(rawData);
            this.processDropAction(zone, type, id, form);
        } catch {
            // Silently fail on invalid drag data
        }
    }

    private resetDragState() {
        this.draggingSourceType = null;
        this.isDraggingOver = null;
    }

    private processDropAction(zone: string, type: string, id: string, form: AdaptiveRuleForm) {
        if (zone === 'action' && type === 'action') {
            this.addAction(form, id);
        } else if (zone === 'condition' && type === 'fact') {
            this.addCondition(form, id);
        } else if (zone === 'deduction' && type === 'virtual-fact') {
            this.addDiagnosis(form, id);
        } else {
            this.triggerInvalidDropFeedback(zone);
        }
    }

    // --- Data Parsing Helpers ---

    public parseInitialFacts(factIds: string[]): AdaptiveRuleFactItem[] {
        return factIds.map((factId) => {
            const fact = this.all_facts.find((f) => f.id === factId);
            const logic = this.parseFactLogic(fact);

            return {
                id: factId,
                key: logic.key || factId,
                name: fact?.name || '',
                operator: logic.op || '==',
                value: logic.val !== undefined ? logic.val : 1,
                isManual: false,
            };
        });
    }

    public parseInitialDeductions(factIds: string[]): AdaptiveRuleDeductionItem[] {
        return factIds.map((factId) => {
            const fact = this.all_facts.find((f) => f.id === factId);
            return {
                id: factId,
                name: fact?.name || '',
                isManual: false,
            };
        });
    }
}

import { BaseState } from '@/states/BaseState.svelte';
import type { AdaptiveFact, AdaptiveAction } from '@/types';

export class AdaptiveRuleEditorState extends BaseState {
    all_facts = $state<AdaptiveFact[]>([]);
    all_actions = $state<AdaptiveAction[]>([]);
    isEdit = $state(false);

    // UI states
    draggingSourceType = $state<string | null>(null);
    isDraggingOver = $state<string | null>(null);
    invalidDropZone = $state<string | null>(null);
    selectedMetadataKey = $state<string>('');

    constructor(data: {
        all_facts: AdaptiveFact[];
        all_actions: AdaptiveAction[];
        isEdit: boolean;
    }) {
        super();
        this.all_facts = data.all_facts;
        this.all_actions = data.all_actions;
        this.isEdit = data.isEdit;

        // Default selected metadata key
        const firstKey = this.METADATA_KEYS[0];
        if (firstKey) {
            this.selectedMetadataKey = firstKey.value;
        }
    }

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

    getNextAutoId(prefix: 'F' | 'V' | 'R', currentList: any[], dbList: any[] = []) {
        const pattern = new RegExp(`^${prefix}(\\d+)$`);
        const extractNums = (list: any[]) =>
            list
                .map((item) => {
                    const id = typeof item === 'string' ? item : item.id || item.key;
                    const match = id?.match(pattern);
                    return match ? parseInt(match[1]) : 0;
                })
                .filter((n) => !isNaN(n));

        const nums = [...extractNums(currentList), ...extractNums(dbList)];
        const maxNum = nums.length > 0 ? Math.max(...nums) : 0;
        return `${prefix}${String(maxNum + 1).padStart(2, '0')}`;
    }

    addCondition(form: any, factId = '') {
        const finalId = factId || this.getNextAutoId('F', form.facts, this.all_facts);
        const existingFact = this.all_facts?.find((f) => f.id === finalId);

        let parsedOp = '==';
        let parsedVal: any = 1;
        let parsedKey = finalId;
        let parsedName = existingFact?.name || '';

        if (existingFact && existingFact.logic) {
            try {
                const logicData =
                    typeof existingFact.logic === 'string'
                        ? JSON.parse(existingFact.logic)
                        : existingFact.logic;
                if (logicData.op) parsedOp = logicData.op;
                if (logicData.val !== undefined) parsedVal = logicData.val;
                if (logicData.key) parsedKey = logicData.key;
            } catch (_e) {}
        }

        form.facts = [
            ...form.facts,
            {
                id: finalId,
                key: parsedKey,
                name: parsedName,
                operator: parsedOp,
                value: parsedVal,
                isManual: !factId,
            },
        ];
    }

    addDiagnosis(form: any, id = '') {
        const finalId = id || this.getNextAutoId('V', form.deduced_facts, this.all_facts);
        const existing = this.all_facts?.find((f) => f.id === finalId);

        if (!form.deduced_facts.find((f: any) => f.id === finalId)) {
            form.deduced_facts = [
                ...form.deduced_facts,
                {
                    id: finalId,
                    name: existing?.name || 'New Virtual Fact',
                    isManual: !id,
                },
            ];
        }
    }

    addAction(form: any, id: string) {
        if (!form.actions.find((a: any) => a.id === id)) {
            form.actions = [...form.actions, { id, metadata: {} }];
        } else {
            this.invalidDropZone = 'action';
            setTimeout(() => (this.invalidDropZone = null), 400);
        }
    }

    addActionMetadata(form: any, index: number) {
        const key = this.selectedMetadataKey;
        if (!key) return;
        if (form.actions[index].metadata[key] !== undefined) {
            alert('Parameter ini sudah ada untuk aksi ini.');
            return;
        }
        form.actions[index].metadata[key] = '';
    }

    removeActionMetadata(form: any, index: number, key: string) {
        const { [key]: _removed, ...rest } = form.actions[index].metadata;
        form.actions[index].metadata = rest;
    }

    handleDragStart(e: DragEvent, id: string, type: string) {
        if (!e.dataTransfer) return;
        const dragData = JSON.stringify({ id, type });
        e.dataTransfer.setData('application/json', dragData);
        e.dataTransfer.effectAllowed = 'copy';
        this.draggingSourceType = type;
    }

    handleDrop(e: DragEvent, zone: 'condition' | 'deduction' | 'action', form: any) {
        e.preventDefault();
        this.draggingSourceType = null;
        this.isDraggingOver = null;

        if (!e.dataTransfer) return;

        try {
            const rawData = e.dataTransfer.getData('application/json');
            if (!rawData) return;
            const dragData = JSON.parse(rawData);
            const { id, type } = dragData;

            if (zone === 'action' && type === 'action') {
                this.addAction(form, id);
            } else if (zone === 'condition' && type === 'fact') {
                this.addCondition(form, id);
            } else if (zone === 'deduction' && type === 'virtual-fact') {
                this.addDiagnosis(form, id);
            } else {
                this.invalidDropZone = zone;
                setTimeout(() => (this.invalidDropZone = null), 400);
            }
        } catch (_err) {}
    }

    parseInitialFacts(factIds: string[]) {
        return factIds.map((factId) => {
            const existing = this.all_facts.find((f) => f.id === factId);
            let parsedOp = '==';
            let parsedVal: any = 1;
            let parsedKey = factId;
            let parsedName = existing?.name || '';

            if (existing && existing.logic) {
                try {
                    const logicData =
                        typeof existing.logic === 'string'
                            ? JSON.parse(existing.logic)
                            : existing.logic;
                    if (logicData.op) parsedOp = logicData.op;
                    if (logicData.val !== undefined) parsedVal = logicData.val;
                    if (logicData.key) parsedKey = logicData.key;
                } catch (_e) {}
            }

            return {
                id: factId,
                key: parsedKey,
                name: parsedName,
                operator: parsedOp,
                value: parsedVal,
                isManual: false,
            };
        });
    }

    parseInitialDeductions(factIds: string[]) {
        return factIds.map((factId) => {
            const existing = this.all_facts.find((f) => f.id === factId);
            return { id: factId, name: existing?.name || '', isManual: false };
        });
    }
}

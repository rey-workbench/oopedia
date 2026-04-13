const DEBUG_MODE = import.meta.env['VITE_ADAPTIVE_DEBUG'] === 'true';

export type ViolationType =
    | 'right_click'
    | 'copy'
    | 'cut'
    | 'paste'
    | 'tab_switch'
    | 'dev_tools_shortcut'
    | 'window_resize';

export type ExamProtectionOptions = {
    blockRightClick?: boolean;
    blockKeyboardShortcuts?: boolean;
    blockCopyPaste?: boolean;
    blockTabSwitch?: boolean;
    blockDevTools?: boolean;
    onViolation?: (type: ViolationType, message: string) => void;
};

const defaultOptions: ExamProtectionOptions = {
    blockRightClick: true,
    blockKeyboardShortcuts: true,
    blockCopyPaste: true,
    blockTabSwitch: true,
    blockDevTools: true,
};

let protectionActive = false;
let violationCallback: ExamProtectionOptions['onViolation'] = undefined;

function triggerViolation(type: ViolationType, message: string) {
    if (violationCallback) {
        violationCallback(type, message);
    }
}

function handleContextMenu(e: MouseEvent) {
    if (!protectionActive) return;
    e.preventDefault();
    triggerViolation('right_click', 'Klik kanan tidak diizinkan selama ujian');
}

function handleKeyDown(e: KeyboardEvent) {
    if (!protectionActive) return;

    const blockedKeys = [
        { key: 'F12' },
        { key: 'i', ctrl: true, shift: true },
        { key: 'j', ctrl: true, shift: true },
        { key: 'c', ctrl: true, shift: true },
        { key: 'u', ctrl: true },
        { key: 's', ctrl: true, alt: true },
        { key: 'p', ctrl: true, alt: true },
        { key: 'c', ctrl: true },
        { key: 'v', ctrl: true },
    ];

    for (const blocked of blockedKeys) {
        const keyMatch =
            e.key === blocked.key || e.key.toLowerCase() === blocked.key?.toLowerCase();
        const ctrlMatch = blocked.ctrl ? e.ctrlKey || e.metaKey : true;
        const shiftMatch = blocked.shift ? e.shiftKey : true;
        const altMatch = blocked.alt ? e.altKey : true;

        if (
            keyMatch &&
            (!blocked.ctrl || ctrlMatch) &&
            (!blocked.shift || shiftMatch) &&
            (!blocked.alt || altMatch)
        ) {
            e.preventDefault();
            if (blocked.key === 'c' && (e.ctrlKey || e.metaKey) && !e.shiftKey) {
                triggerViolation('copy', 'Copy tidak diizinkan selama ujian');
            } else if (blocked.key === 'v' && (e.ctrlKey || e.metaKey)) {
                triggerViolation('paste', 'Paste tidak diizinkan selama ujian');
            } else {
                triggerViolation('dev_tools_shortcut', 'Keyboard shortcut tidak diizinkan');
            }
            return;
        }
    }
}

function handleVisibilityChange() {
    if (!protectionActive) return;
    if (document.hidden) {
        triggerViolation('tab_switch', 'Pindah tab tidak diizinkan selama ujian');
    }
}

function handleWindowBlur() {
    if (!protectionActive) return;

    const threshold = 160;
    const widthThreshold = window.outerWidth - window.innerWidth > threshold;
    const heightThreshold = window.outerHeight - window.innerHeight > threshold;

    if (widthThreshold || heightThreshold) {
        triggerViolation('window_resize', 'Mengubah ukuran window tidak diizinkan');
    }
}

export function activateExamProtection(options: Partial<ExamProtectionOptions> = {}): void {
    if (DEBUG_MODE) {
        console.debug('[ExamProtection] Debug mode active - protection disabled');
        return;
    }

    const opts = { ...defaultOptions, ...options };
    protectionActive = true;
    violationCallback = opts.onViolation;

    if (opts.blockRightClick) {
        document.addEventListener('contextmenu', handleContextMenu);
    }

    if (opts.blockKeyboardShortcuts) {
        document.addEventListener('keydown', handleKeyDown);
    }

    if (opts.blockCopyPaste) {
        document.addEventListener('copy', (e) => {
            e.preventDefault();
            triggerViolation('copy', 'Copy tidak diizinkan selama ujian');
        });
        document.addEventListener('cut', (e) => {
            e.preventDefault();
            triggerViolation('cut', 'Cut tidak diizinkan selama ujian');
        });
        document.addEventListener('paste', (e) => {
            e.preventDefault();
            triggerViolation('paste', 'Paste tidak diizinkan selama ujian');
        });
    }

    if (opts.blockTabSwitch) {
        document.addEventListener('visibilitychange', handleVisibilityChange);
        window.addEventListener('blur', () => {
            if (protectionActive) {
                triggerViolation('tab_switch', 'Pindah tab tidak diizinkan selama ujian');
            }
        });
    }

    if (opts.blockDevTools) {
        setInterval(handleWindowBlur, 1000);
    }

    console.debug('[ExamProtection] Protection activated');
}

export function deactivateExamProtection(): void {
    protectionActive = false;
    violationCallback = undefined;

    document.removeEventListener('contextmenu', handleContextMenu);
    document.removeEventListener('keydown', handleKeyDown);
    document.removeEventListener('visibilitychange', handleVisibilityChange);

    console.debug('[ExamProtection] Protection deactivated');
}

export function isProtectionActive(): boolean {
    return protectionActive;
}

export function isDebugMode(): boolean {
    return DEBUG_MODE;
}

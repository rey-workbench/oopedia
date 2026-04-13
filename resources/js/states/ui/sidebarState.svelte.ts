/**
 * Sidebar State - Svelte 5 native state
 * Manages sidebar visibility across the application.
 */
class SidebarState {
    isOpen = $state(false);
    isClosing = $state(false);

    open() {
        this.isOpen = true;
        this.isClosing = false;
    }

    close() {
        this.isClosing = true;
        setTimeout(() => {
            this.isOpen = false;
            this.isClosing = false;
        }, 300); // Match transition duration
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    setOpen(value: boolean) {
        this.isOpen = value;
        this.isClosing = false;
    }
}

export const sidebarState = new SidebarState();

export function initSidebarResponsive() {
    if (typeof window !== 'undefined') {
        const handleResize = () => {
            if (window.innerWidth >= 1024) {
                sidebarState.setOpen(false);
            }
        };

        window.addEventListener('resize', handleResize);
        handleResize();

        return () => window.removeEventListener('resize', handleResize);
    }

    return () => {};
}

export function openSidebar() {
    sidebarState.open();
}

export function closeSidebar() {
    sidebarState.close();
}

export function toggleSidebar() {
    sidebarState.toggle();
}

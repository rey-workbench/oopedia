/**
 * Sidebar State - Svelte 5 native state
 * Manages sidebar visibility across the application.
 */
class SidebarState {
    isOpen = $state(false);
    isDesktop = $state(false);

    open() {
        this.isOpen = true;
    }

    close() {
        this.isOpen = false;
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
    }
}

export const sidebarState = new SidebarState();

export function initSidebarResponsive() {
    if (typeof window !== 'undefined') {
        const handleResize = () => {
            sidebarState.isDesktop = window.innerWidth >= 1024;
            if (sidebarState.isDesktop) {
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

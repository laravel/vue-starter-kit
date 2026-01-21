import type { ComputedRef, Ref } from 'vue';

export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';

export type SidebarContext = {
    state: ComputedRef<'expanded' | 'collapsed'>;
    open: Ref<boolean>;
    setOpen: (value: boolean) => void;
    isMobile: Ref<boolean>;
    openMobile: Ref<boolean>;
    setOpenMobile: (value: boolean) => void;
    toggleSidebar: () => void;
};

export type AppShellVariant = 'header' | 'sidebar';

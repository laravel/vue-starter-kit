import { AppPageProps } from '@/types/index';
import { createHeadManager, Page, Router } from '@inertiajs/core';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;

        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

// Declare shared props for Inertia and App pages...
declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}

// Add typings for Inertia's $page and $headManager properties...
declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $inertia: typeof Router;
        $page: Page;
        $headManager: ReturnType<typeof createHeadManager>;
        route: AppRouter;
    }
}

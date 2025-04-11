import type { route as routeFn } from 'ziggy-js';

declare global {
    const route: typeof routeFn;
}

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        route: typeof routeFn;
    }
}

import { clsx, type ClassValue } from 'clsx';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { twMerge } from 'tailwind-merge';
import { DefineComponent } from 'vue';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function resolveTitle(title: string | null): string {
    const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

    return title ? `${title} - ${appName}` : appName;
}

export function resolvePage(name: string) {
    return resolvePageComponent(`../pages/${name}.vue`, import.meta.glob<DefineComponent>('../pages/**/*.vue'));
}

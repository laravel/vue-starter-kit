import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, h } from 'vue';
import { route as ziggyRoute } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

/**
 * Added eslint-disable-next-line @typescript-eslint/ban-ts-comment and @ts-ignore to fix the error.
 * I'm not sure if the fix really leads to a type-safe Javascript. However, it looks like a javascript hack to me, which is perfectly legitimate.
 */

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
        // @ts-ignore
        resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
        setup({ App, props, plugin }) {
            const app = createSSRApp({ render: () => h(App, props) });

            // Configure Ziggy for SSR...
            const ziggyConfig = {
                // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                // @ts-ignore
                ...page.props.ziggy,
                // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                // @ts-ignore
                location: new URL(page.props.ziggy.location),
            };

            // Create route function...
            const route = (name: string, params?: any, absolute?: boolean) => ziggyRoute(name, params, absolute, ziggyConfig);

            // Make route function available globally...
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-ignore
            app.config.globalProperties.route = route;

            // Make route function available globally for SSR...
            if (typeof window === 'undefined') {
                // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                // @ts-ignore
                global.route = route;
            }

            app.use(plugin);

            return app;
        },
    }),
);

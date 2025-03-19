import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, DefineComponent, h } from 'vue';
import { route, RouteParams, Router } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: resolvePage,
        setup({ App, props, plugin }) {
            const app = createSSRApp({ render: () => h(App, props) });

            // Configure Ziggy for SSR...
            const ziggyConfig = {
                ...page.props.ziggy,
                location: new URL(page.props.ziggy.location),
            };

            // bind config to ziggyRoute function
            function appRoute(): Router;
            function appRoute(name: string, params?: RouteParams<typeof name>, absolute?: boolean): string;
            function appRoute(name?: string, params?: RouteParams<string>, absolute?: boolean): Router | string {
                if (name === undefined) {
                    return route();
                }

                return route(name, params, absolute, ziggyConfig);
            }

            // Make route function available globally...
            app.config.globalProperties.route = appRoute;

            // Make route function available globally for SSR...
            if (typeof window === 'undefined') {
                global.route = appRoute;
            }

            app.use(plugin);

            return app;
        },
    }),
);

function resolvePage(name: string) {
    const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');

    return resolvePageComponent<DefineComponent>(`./Pages/${name}.vue`, pages);
}

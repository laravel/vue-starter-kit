import { resolvePage, resolveTitle } from '@/lib/utils';
import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { createPinia } from 'pinia';
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';

const pinia = createPinia();

createServer(
	(page) =>
		createInertiaApp({
			page,
			render: renderToString,
			title: resolveTitle,
			resolve: resolvePage,
			setup: ({ App, props, plugin }) =>
				createSSRApp({ render: () => h(App, props) })
					.use(plugin)
					.use(pinia),
		}),
	{ cluster: true },
);

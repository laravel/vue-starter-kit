import '../css/app.css';

import { resolvePage, resolveTitle } from '@/lib/utils';
import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import { createSSRApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

const pinia = createPinia();

createInertiaApp({
	title: resolveTitle,
	resolve: resolvePage,
	setup({ el, App, props, plugin }) {
		createSSRApp({ render: () => h(App, props) })
			.use(plugin)
			.use(pinia)
			.mount(el);
	},
	progress: {
		color: '#4B5563',
	},
});

// This will set light / dark mode on page load...
initializeTheme();

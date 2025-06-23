import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { transformer } from './resources/js/lib/vite-plugin-transformer';

export default defineConfig({
	plugins: [
		laravel({
			input: ['resources/js/app.ts'],
			ssr: 'resources/js/ssr.ts',
			refresh: true,
		}),
		wayfinder(),
		transformer(),
		tailwindcss(),
		vue({
			template: {
				transformAssetUrls: {
					base: null,
					includeAbsolute: false,
				},
			},
		}),
	],
});

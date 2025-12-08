import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import path from 'path';
import fs from 'fs';

const stubPath = path.resolve(__dirname, 'resources/js/lib/_wayfinder-stub.ts');

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: [
            {
                find: /^@\/routes\/(.+)$/,
                replacement: (match, p1) => {
                    const target = path.resolve(__dirname, `resources/js/routes/${p1}/index.ts`);

                    const mockPath = path.resolve(__dirname, `resources/js/mocks/routes/${p1}.ts`);

                    const exists = fs.existsSync(target);

                    if (! exists) {
                        console.debug(`Resolving route module: ${p1} (${target}) -> ${exists ? 'actual file' : 'stub'}`);
                    }

                    return fs.existsSync(target) ? target : mockPath;
                },
            },
        ],
    },
});

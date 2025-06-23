import { exec } from 'child_process';
import { minimatch } from 'minimatch';
import osPath from 'path';
import { PluginContext } from 'rollup';
import { promisify } from 'util';
import { HmrContext, Plugin } from 'vite';

const execAsync = promisify(exec);

interface TransformerOptions {
	patterns?: string[];
	command?: string;
}

let context: PluginContext;

export const transformer = ({
	patterns = ['app/Data/**/*.php'],
	command = 'php artisan typescript:transform && bun prettier --write resources/js/types/generated.d.ts',
}: TransformerOptions = {}): Plugin => {
	patterns = patterns.map((pattern) => pattern.replace('\\', '/'));

	const runCommand = async () => {
		try {
			await execAsync(`${command}`);
		} catch (error) {
			context.error('Error transforming: ' + error);
		}

		context.info(`Transformed`);
	};

	return {
		name: '@laravel/vite-plugin-transformer',
		enforce: 'pre',
		buildStart() {
			// oxlint-disable-next-line no-this-alias
			context = this;
			return runCommand();
		},
		async handleHotUpdate({ file, server }) {
			if (shouldRun(patterns, { file, server })) {
				await runCommand();
			}
		},
	};
};

const shouldRun = (patterns: string[], opts: Pick<HmrContext, 'file' | 'server'>): boolean => {
	const file = opts.file.replaceAll('\\', '/');

	return patterns.some((pattern) => {
		pattern = osPath.resolve(opts.server.config.root, pattern).replaceAll('\\', '/');

		return minimatch(file, pattern);
	});
};

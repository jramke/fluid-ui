import { defineConfig } from 'tsdown';

export default defineConfig([
	{
		entry: [
			'./Resources/Private/Client/index.ts',
			'./Resources/Private/Primitives/Dialog/source/dialog.ts',
			'./Resources/Private/Primitives/Clipboard/source/clipboard.ts',
			'./Resources/Private/Primitives/Collapsible/source/collapsible.ts',
			'./Resources/Private/Primitives/ScrollArea/source/scroll-area.ts',
			'./Resources/Private/Primitives/Tooltip/source/tooltip.ts',
		],
		platform: 'browser',
		dts: true,
		outDir: './Resources/Public/JavaScript/dist',
		clean: true,
		minify: true,
	},
]);

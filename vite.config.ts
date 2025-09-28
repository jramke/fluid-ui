import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';
import typo3 from 'vite-plugin-typo3';

export default defineConfig({
	plugins: [typo3(), tailwindcss()],
});

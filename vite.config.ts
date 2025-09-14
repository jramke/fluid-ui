import { defineConfig } from 'vite';
import typo3 from 'vite-plugin-typo3';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
	plugins: [typo3(), tailwindcss()],
});

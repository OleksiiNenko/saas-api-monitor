import { defineConfig } from 'vitest/config';

// Note: we deliberately do NOT import @vitejs/plugin-react here. On Node 18
// (this environment) Vite 7 bundles the config into node_modules/.vite-temp and
// the plugin import fails to resolve there. esbuild's automatic JSX transform
// (below) compiles JSX with React 19's runtime without needing the plugin.
export default defineConfig({
    esbuild: {
        jsx: 'automatic',
    },
    test: {
        environment: 'jsdom',
        globals: true,
        include: ['resources/js/**/*.{test,spec}.{js,jsx}'],
        setupFiles: ['resources/js/test-setup.js'],
    },
});

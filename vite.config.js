import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// Note: we intentionally avoid @vitejs/plugin-react. On Node 18 (this
// environment) Vite 7 bundles the config into node_modules/.vite-temp and the
// plugin import fails to resolve from there. esbuild's automatic JSX transform
// compiles .jsx with React 19's runtime in both dev and build. The only thing
// lost is React Fast Refresh (HMR does a full reload instead) — acceptable here.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    esbuild: {
        jsx: 'automatic',
    },
    optimizeDeps: {
        esbuildOptions: {
            jsx: 'automatic',
        },
    },
});

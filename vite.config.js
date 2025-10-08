import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import legacy from '@vitejs/plugin-legacy';

export default defineConfig(({ command }) => ({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/analytics.js', 'resources/js/webcrypto-wrapper.js'],
            refresh: true,
        }),
        // Enable Tailwind in both dev and prod so utilities render in built CSS
        tailwindcss(),
        // Produce a legacy (nomodule) bundle for older browsers (adds SystemJS + polyfills)
        legacy({
            targets: ['defaults', 'ie >= 11'],
            additionalLegacyPolyfills: ['regenerator-runtime/runtime'],
            renderLegacyChunks: true,
            modernPolyfills: true,
        }),
    ],
    // Avoid bundling snarkjs (very heavy) to keep transform/build responsive
    build: {
        chunkSizeWarningLimit: 1200,
        rollupOptions: {
            external: ['snarkjs'],
            output: {
                manualChunks: {
                    // Split frequently used vendor libs
                    'vendor-highlight': ['highlight.js'],
                    'vendor-qrcode': ['qrcode'],
                    'vendor-srp': ['secure-remote-password/client', 'secure-remote-password/lib/srp-integer'],
                },
            },
        },
    },
    optimizeDeps: {
        exclude: ['snarkjs'],
    },
}));

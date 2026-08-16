import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import { sentryVitePlugin } from '@sentry/vite-plugin';

export default defineConfig(({ mode }) => {
    // loadEnv reads both Bifrost-injected environment variables and local .env files.
    const env = loadEnv(mode, process.cwd(), '');
    const sentryRelease = env.VITE_SENTRY_RELEASE;
    const sentryUploadConfigured = Boolean(
        sentryRelease
        && env.SENTRY_AUTH_TOKEN
        && env.SENTRY_ORG
        && env.SENTRY_PROJECT,
    );

    return {
    build: {
        manifest: 'manifest.json',
        // Source maps exist only while Bifrost has the Sentry upload credentials.
        // The plugin removes them after upload so they are not shipped to users.
        sourcemap: sentryUploadConfigured ? 'hidden' : false,
        rollupOptions: {
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'assets/app.css';
                    }
                    return 'assets/[name][extname]';
                },
            },
        },
    },
    server: {
        host: 'localtest.me',
        port: 5173,
        strictPort: true,
        cors: true,
        origin: 'http://localtest.me:5173',
        hmr: {
            host: 'localtest.me',
            protocol: 'ws',
            port: 5173,
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        ...(sentryUploadConfigured ? [sentryVitePlugin({
            authToken: env.SENTRY_AUTH_TOKEN,
            org: env.SENTRY_ORG,
            project: env.SENTRY_PROJECT,
            release: {
                name: sentryRelease,
            },
            sourcemaps: {
                filesToDeleteAfterUpload: ['public/build/**/*.map'],
            },
            telemetry: false,
        })] : []),
    ],
    };
});

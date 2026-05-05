import laravel from 'laravel-vite-plugin';
import { defineConfig, loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');
  const watchWorkers = env.WATCH_WORKER === 'true' || false;

  return {
    plugins: [
      laravel({
        input: [],
        publicDirectory: 'public',
        buildDirectory: 'build/workers'
      })
    ],
    build: {
      manifest: false,
      rollupOptions: {
        input: {
          participantGroupGenerator: 'resources/js/components/participantGroups/index.worker.js',
        },
        output: {
          entryFileNames: '[name].worker.js'
        }
      },
      watch: watchWorkers ? {} : undefined
    }
  };
});

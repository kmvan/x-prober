import react from '@vitejs/plugin-react'
import { dirname } from 'node:path'
import { fileURLToPath } from 'node:url'
import { defineConfig, loadEnv } from 'vite'
import tsconfigPaths from 'vite-tsconfig-paths'
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  return {
    root: './dev',
    envDir: './',
    server: {
      proxy: {
        '/api': {
          target: 'http://localhost:8000/api.php',
          changeOrigin: true,
          rewrite: (path) => path.replace(/^\/api/, ''),
        },
      },
    },
    resolve: {
      alias: {
        '@': dirname(fileURLToPath(import.meta.url)) + '/src',
      },
    },
    css: {
      modules: {
        generateScopedName: '[name]__[local]_[hash]',
      },
    },
    plugins: [react(), tsconfigPaths()],
    build: {
      outDir: '../dist',
      manifest: true,
      target: 'esnext',
      // rollupOptions: {
      //   external: ['react', 'react-dom'],
      //   output: {
      //     globals: {
      //       react: 'React',
      //       'react-dom': 'ReactDOM',
      //     },
      //   },
      // },

      // rollupOptions: {
      //   input: new URL('./src/main.tsx', import.meta.url).pathname,
      // },
    },
    define: {
      VITE_PORT: JSON.stringify(env.VITE_PORT),
    },
  }
})

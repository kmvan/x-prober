import react from '@vitejs/plugin-react'
import { existsSync, mkdirSync } from 'fs'
import { dirname, resolve } from 'path'
import { fileURLToPath } from 'url'
import { defineConfig } from 'vite'
import tsconfigPaths from 'vite-tsconfig-paths'
const __dirname = dirname(fileURLToPath(import.meta.url))
const tmpDir = resolve(__dirname, '.tmp')
if (!existsSync(tmpDir)) {
  mkdirSync(tmpDir)
}
export default defineConfig({
  mode: 'production',
  root: __dirname,
  build: {
    lib: {
      entry: resolve(__dirname, 'src/main.tsx'),
      name: 'app',
      fileName: () => 'app.js',
      formats: ['umd'],
      cssFileName: 'app',
    },
    outDir: tmpDir,
    sourcemap: 'hidden',
    emptyOutDir: true,
    target: 'esnext',
    // rollupOptions:{
    //   output: {
    //     assetFileNames: (assetInfo) => {
    //       for(const name of assetInfo.names) {
    //         if (name.endsWith('.css')) {
    //           return 'app.css';
    //         }
    //       }
    //     }
    //   }
    // }
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src/'),
    },
    extensions: ['.ts', '.tsx', '.js', '.mjs'],
  },
  define: {
    __DEV__: false,
    DEBUG: false,
    'process.env': {
      NODE_ENV: JSON.stringify('production'),
      WEBPACK_ENV: JSON.stringify('production'),
    },
  },
  plugins: [react(), tsconfigPaths()],
})

import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'
export default defineConfig({
  // css: {
  //   modules: {
  //     generateScopedName: 'fc-mde__[local]_[hash]',
  //   },
  // },
  plugins: [
    react(),
    // dts({
    //   tsconfigPath: './tsconfig.build.json',
    //   insertTypesEntry: true,
    //   outDir: 'dist/types',
    // }),
  ],
  build: {
    sourcemap: true,
    // lib: {
    //   entry: 'src/index.ts',
    //   name: 'fc-mde',
    //   fileName: (format) =>
    //     ({
    //       cjs: `index.cjs`,
    //     })[format] ?? `index.${format}.js`,
    //   formats: ['es', 'cjs'],
    // },
    // rollupOptions: {
    //   external: ['react', 'react-dom'],
    //   output: {
    //     globals: {
    //       react: 'React',
    //       'react-dom': 'ReactDOM',
    //     },
    //   },
    // },
    target: 'esnext',
  },
})

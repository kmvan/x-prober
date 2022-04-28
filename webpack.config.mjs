import path, { dirname } from 'path'
import { createTransformer } from 'typescript-plugin-styled-components'
import { fileURLToPath } from 'url'
import webpack from 'webpack'
import { rmFiles } from './tools/rm-files.mjs'
const __dirname = dirname(fileURLToPath(import.meta.url))
rmFiles(path.resolve(__dirname, '.tmp'))
export default {
  mode: 'development',
  entry: {
    app: path.resolve(
      __dirname,
      'src/Components/Bootstrap/components/index.tsx'
    ),
  },
  output: {
    path: path.resolve(__dirname, '.tmp'),
    filename: '[name].js',
    publicPath: './.tmp',
  },
  resolve: {
    extensions: ['.ts', '.tsx', '.js', '.mjs'],
  },
  plugins: [
    new webpack.DefinePlugin({
      __DEV__: true,
      'process.env': {
        NODE_ENV: JSON.stringify('development'),
        WEBPACK_ENV: JSON.stringify('development'),
      },
      DEBUG: true,
    }),
  ],
  module: {
    rules: [
      {
        test: /\.(ts|tsx)$/,
        exclude: /node_modules/,
        include: path.resolve(__dirname, 'src'),
        use: [
          {
            loader: 'ts-loader',
            options: {
              transpileOnly: true,
              getCustomTransformers: () => ({
                before: [createTransformer()],
              }),
            },
          },
        ],
      },
    ],
  },
  devtool: 'source-map',
  watch: true,
}

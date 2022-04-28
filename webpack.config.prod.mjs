import path from 'path'
import TerserPlugin from 'terser-webpack-plugin'
import { createTransformer } from 'typescript-plugin-styled-components'
import webpack from 'webpack'
import { rmFiles } from './tools/rm-files.mjs'
rmFiles(path.resolve(__dirname, '.tmp'))
export default {
  mode: 'production',
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
    alias: {
      root: __dirname,
    },
  },
  optimization: {
    nodeEnv: 'production',
    removeAvailableModules: true,
    minimize: true,
    minimizer: [
      new TerserPlugin({
        parallel: true,
        extractComments: false,
        terserOptions: {
          compress: true,
          ecma: 2016,
          keep_classnames: false,
          keep_fnames: false,
          module: false,
          sourceMap: false,
          format: {
            ascii_only: true,
            comments: false,
          },
        },
      }),
    ],
  },
  plugins: [
    new webpack.DefinePlugin({
      __DEV__: false,
      'process.env': {
        NODE_ENV: JSON.stringify('production'),
        WEBPACK_ENV: JSON.stringify('production'),
      },
      DEBUG: false,
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
                before: [
                  createTransformer({
                    minify: true,
                  }),
                ],
              }),
            },
          },
        ],
      },
    ],
  },
  devtool: 'hidden-source-map',
}

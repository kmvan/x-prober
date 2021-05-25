'use strict'

const webpack = require('webpack')
const TerserPlugin = require('terser-webpack-plugin')
const path = require('path')
const createStyledComponentsTransformer = require('typescript-plugin-styled-components')
  .default
const styledComponentsTransformer = createStyledComponentsTransformer()
const rimraf = require('rimraf')

rimraf('.tmp', {}, () => {})

module.exports = {
  mode: 'production',
  entry: {
    app: './src/Components/Bootstrap/src/components/index.tsx',
  },
  output: {
    path: path.resolve(__dirname, '.tmp'),
    filename: '[name].js',
    publicPath: './.tmp',
  },
  resolve: {
    extensions: ['.ts', '.tsx', '.js'],
    alias: {
      root: __dirname,
      '@': path.resolve(__dirname, 'src/Components'),
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
                before: [styledComponentsTransformer],
              }),
            },
          },
        ],
      },
      {
        test: /\.(png|jpg|gif)$/i,
        use: [
          {
            loader: 'url-loader',
            options: {
              // limit: 8192
            },
          },
        ],
      },
    ],
  },
  devtool: 'hidden-source-map',
}

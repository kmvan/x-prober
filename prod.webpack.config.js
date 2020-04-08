'use strict'

const webpack = require('webpack')
const path = require('path')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')
const ShakePlugin = require('webpack-common-shake')
const TerserPlugin = require('terser-webpack-plugin')
const createStyledComponentsTransformer = require('typescript-plugin-styled-components')
  .default
const styledComponentsTransformer = createStyledComponentsTransformer()

// alias
let alias = {
  root: __dirname,
  components: path.resolve(__dirname, 'src/Components'),
}
// set alias with ~
Object.entries(alias).map(item => {
  alias[`~${item[0]}`] = item[1]
})

module.exports = {
  mode: 'production',
  entry: {
    app: path.resolve(
      __dirname,
      'src/Components/Bootstrap/src/components/index.tsx'
    ),
  },
  output: {
    path: path.resolve(__dirname, '.tmp'),
    filename: '[name].js',
    publicPath: './.tmp',
  },
  resolve: {
    extensions: ['.ts', '.tsx', '.js'],
    alias: alias || {},
  },
  optimization: {
    minimizer: [
      new TerserPlugin({
        parallel: true,
        cache: true,
        terserOptions: {
          output: {
            ecma: 6,
            comments: false,
          },
          compress: {
            unsafe_comps: true,
            unsafe_Function: true,
            unsafe_math: true,
            unsafe_methods: true,
            unsafe_proto: true,
            warnings: true,
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
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: ['.tmp'],
    }),
    new ShakePlugin.Plugin(),
    new webpack.optimize.ModuleConcatenationPlugin(),
  ],
  module: {
    rules: [
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
      {
        test: /\.(ts|tsx)$/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              include: path.resolve(__dirname, 'src'),
            },
          },
        ],
      },
    ],
  },
  stats: {
    modules: false,
    reasons: true,
    errorDetails: true,
    timings: false,
  },
  devtool: 'hidden',
}

'use strict'

global.__DEV__ = process.env.WEBPACK_ENV === 'development'
global.__TEST__ = process.env.WEBPACK_ENV === 'test'

const __DEV__ = global.__DEV__
const __TEST__ = global.__TEST__
const webpack = require('webpack')
const path = require('path')
const LodashModuleReplacementPlugin = require('lodash-webpack-plugin')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')
const ShakePlugin = require('webpack-common-shake')
const TerserPlugin = require('terser-webpack-plugin')
const createStyledComponentsTransformer = require('typescript-plugin-styled-components')
  .default
const styledComponentsTransformer = createStyledComponentsTransformer()

console.log(`Run in ${process.env.WEBPACK_ENV}`)

// alias
let alias = {
  root: __dirname,
  components: path.resolve(__dirname, 'src/Components'),
}
// set alias with ~
Object.entries(alias).map(item => {
  alias[`~${item[0]}`] = item[1]
})

// plugins
let plugins = [
  new webpack.DefinePlugin({
    __DEV__,
    'process.env': {
      NODE_ENV: __DEV__
        ? JSON.stringify('development')
        : JSON.stringify('production'),
    },
    DEBUG: __DEV__,
  }),
  new CleanWebpackPlugin({
    cleanOnceBeforeBuildPatterns: ['.tmp'],
  }),
  new LodashModuleReplacementPlugin({
    shorthands: true,
    collections: true,
    paths: true,
  }),
  new ShakePlugin.Plugin(),
]

// dev plugins
if (!__DEV__) {
  plugins = plugins.concat([new webpack.optimize.ModuleConcatenationPlugin()])
}

let config = {
  mode: __DEV__ ? 'development' : 'production',
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
  plugins,
  module: {
    rules: [
      {
        test: /\.(ts|tsx)$/,
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
        exclude: __DEV__ ? /node_modules/ : undefined,
        include: path.resolve(__dirname, 'src'),
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
  stats: {
    modules: __DEV__,
    reasons: true,
    errorDetails: true,
    timings: __DEV__,
  },
  devtool: __DEV__ || __TEST__ ? 'source-map' : 'hidden',
  watchOptions: {
    poll: 1000,
    aggregateTimeout: 1000,
    ignored: /node_modules/,
  },
}

// babel config
if (!__DEV__ || __TEST__) {
  config.module.rules.unshift({
    test: /\.(js|ts|tsx)$/,
    use: [
      {
        loader: 'babel-loader',
        options: {
          presets: ['@babel/preset-env'],
        },
      },
    ],
  })
}

module.exports = config

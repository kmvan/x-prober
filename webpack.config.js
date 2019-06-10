'use strict'

global.__DEV__ = process.env.WEBPACK_ENV === 'development'
global.__TEST__ = process.env.WEBPACK_ENV === 'test'

const __DEV__ = global.__DEV__
const webpack = require('webpack')
const path = require('path')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const node_modules = path.resolve(__dirname, 'node_modules')
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin')
const LodashModuleReplacementPlugin = require('lodash-webpack-plugin')
const glob = require('glob')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')
const ShakePlugin = require('webpack-common-shake')

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
    cleanOnceBeforeBuildPatterns: ['tmp'],
  }),
  new ExtractTextPlugin({
    filename: getPath => {
      return getPath('[name].css')
    },
    allChunks: true,
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
  plugins = plugins.concat([
    new webpack.optimize.ModuleConcatenationPlugin(),
    new webpack.optimize.LimitChunkCountPlugin(),
    new OptimizeCssAssetsPlugin({
      assetNameRegExp: /\.css$/g,
      cssProcessor: require('cssnano'),
      cssProcessorOptions: {
        discardComments: { removeAll: true },
      },
      canPrint: true,
    }),
    new UglifyJsPlugin({
      cache: false,
      parallel: true,
      uglifyOptions: {
        mangle: {
          eval: true,
          toplevel: true,
        },
        parse: {
          html5_comments: false,
        },
        output: {
          comments: false,
          beautify: false,
        },
        ecma: 5,
        ie8: false,
        compress: {
          drop_console: true,
          drop_debugger: true,
          expression: true,
          hoist_funs: true,
          // hoist_vars:true,
          keep_fargs: false,
          keep_fnames: false,
          unsafe: true,
          unsafe_comps: true,
          unsafe_Function: true,
          unsafe_math: true,
          unsafe_regexp: true,
          unsafe_undefined: true,
        },
      },
      sourceMap: false,
    }),
  ])
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
    path: path.resolve(__dirname, 'tmp'),
    filename: '[name].js',
    publicPath: './tmp',
  },
  resolve: {
    extensions: ['.ts', '.tsx', '.js', '.scss', '.sass', '.css'],
    alias: alias || {},
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
            },
          },
        ],
        exclude: __DEV__ ? /node_modules/ : undefined,
        include: path.resolve(__dirname, 'src'),
      },
      {
        test: /\.(scss)$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: [
            {
              loader: 'css-loader',
              options: {
                sourceMap: __DEV__,
                //   minimize: !__DEV__,
              },
            },
            {
              loader: 'postcss-loader',
              options: {
                sourceMap: __DEV__,
                ident: 'postcss',
                plugins: () => [
                  require('postcss-flexbugs-fixes')(),
                  require('postcss-preset-env')({
                    stage: 0,
                  }),
                  require('autoprefixer')(),
                ],
              },
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: __DEV__,
              },
            },
          ],
        }),
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

'use strict'

const webpack = require('webpack')
const path = require('path')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')
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

module.exports = {
  mode: 'development',
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
  plugins: [
    new webpack.DefinePlugin({
      __DEV__: true,
      'process.env': {
        NODE_ENV: JSON.stringify('development'),
        WEBPACK_ENV: JSON.stringify('development'),
      },
      DEBUG: true,
    }),
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: ['.tmp'],
    }),
  ],
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
        exclude: /node_modules/,
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
    modules: true,
    reasons: true,
    errorDetails: true,
    timings: true,
  },
  devtool: 'source-map',
  watchOptions: {
    poll: 1000,
    aggregateTimeout: 1000,
    ignored: /node_modules/,
  },
}

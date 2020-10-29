'use strict'

const webpack = require('webpack')
const path = require('path')
const createStyledComponentsTransformer = require('typescript-plugin-styled-components')
  .default
const styledComponentsTransformer = createStyledComponentsTransformer()
const rimraf = require('rimraf')

rimraf('.tmp', {}, () => {})

module.exports = {
  mode: 'development',
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
      // {
      //   test: /\.m?js$/,
      //   type: 'javascript/auto',
      //   resolve: {
      //     fullySpecified: false,
      //   },
      // },
      {
        test: /\.(ts|tsx)$/,
        // type: 'javascript/auto',
        // resolve: {
        //   fullySpecified: false,
        // },
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
  // stats: {
  //   modules: true,
  //   reasons: true,
  //   errorDetails: true,
  //   timings: true,
  // },
  devtool: 'source-map',
  watch: true,
  // watchOptions: {
  //   poll: 1000,
  //   aggregateTimeout: 1000,
  //   ignored: /node_modules/,
  // },
}

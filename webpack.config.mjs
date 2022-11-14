import { mkdirSync } from 'fs'
import MiniCssExtractPlugin from 'mini-css-extract-plugin'
import path, { dirname } from 'path'
import { fileURLToPath } from 'url'
import webpack from 'webpack'
import { rmFiles } from './tools/rm-files.mjs'
const __dirname = dirname(fileURLToPath(import.meta.url))
rmFiles(path.resolve(__dirname, '.tmp'))
mkdirSync(path.resolve(__dirname, '.tmp'))
export default {
  mode: 'development',
  entry: {
    app: path.resolve(
      __dirname,
      'src/Components/Bootstrap/components/index.tsx',
    ),
  },
  output: {
    path: path.resolve(__dirname, '.tmp'),
    filename: '[name].js',
    publicPath: './.tmp',
  },
  resolve: {
    extensions: ['.ts', '.tsx', '.js', '.mjs', '.json', '.scss'],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].css',
      chunkFilename: '[id].css',
      ignoreOrder: false, // Enable to remove warnings about conflicting order
    }),
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
            },
          },
        ],
      },
      {
        test: /\.css$/,
        use: [
          'style-loader',
          {
            loader: 'css-loader',
            options: {
              import: false,
              modules: true,
            },
          },
        ],
      },
      {
        test: /\.css$/i,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
      },
      {
        test: /\.(scss)$/i,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              importLoaders: 1,
              modules: {
                localIdentName: '[path][name]__[local]',
              },
            },
          },
          'sass-loader',
        ],
      },
    ],
  },
  devtool: 'source-map',
  watch: true,
}

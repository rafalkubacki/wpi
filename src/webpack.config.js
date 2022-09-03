const glob = require('glob')
const path = require('path')
const StylelintPlugin = require('stylelint-webpack-plugin')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
const MiniCssExtractPlugin = require("mini-css-extract-plugin")
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin")
const TerserPlugin = require("terser-webpack-plugin")

let entry = {
  "base": ["./styles/base.scss", "./scripts/base.js"],
}

glob.sync("./styles/pages/*.scss").map(file => {
  if (entry[path.basename(file, ".scss")]) {
    entry[path.basename(file, ".scss")].push(file)
  } else {
    entry[path.basename(file, ".scss")] = [file]
  }
})

glob.sync("./scripts/pages/*.js").map(file => {
  if (entry[path.basename(file, ".js")]) {
    entry[path.basename(file, ".js")].push(file)
  } else {
    entry[path.basename(file, ".js")] = [file]
  }
})

module.exports = {
  target: 'web',
  mode: 'production',
  resolve: {
    alias: {
      'styles': path.join(__dirname, 'styles'),
      'scripts': path.join(__dirname, 'scripts'),
    }
  },
  entry,
  output: {
    filename: '[name].bundle.js',
    path: path.resolve('../theme/', 'assets'),
    clean: true,
  },
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              ['@babel/preset-env', { targets: "defaults" }]
            ]
          }
        }
      },
      {
        test: /\.css$/i,
        use: [MiniCssExtractPlugin.loader, "css-loader"],
      },
      {
        test: /\.s[ac]ss$/i,
        use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
      },
    ],
  },
  optimization: {
    minimize: true,
    minimizer: [
      new TerserPlugin(),
      new CssMinimizerPlugin(),
    ],
  },
  plugins: [
    new StylelintPlugin(),
    new MiniCssExtractPlugin({
      filename: '[name].bundle.css'
    }),
    new BrowserSyncPlugin({
      files: ['../theme/**/*.php', '../theme/**/*.twig'],
      proxy: 'http://localhost:8080/'
    })
  ],
};

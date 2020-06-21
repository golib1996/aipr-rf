const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  entry: {
    app: "./src/main.js"
  },
  output: {
    filename: "research.js",
    path: path.resolve(__dirname, "./wp-content/themes/enfold/js/"),
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "../css/research.css"
    })
  ],
  module: {
    rules: [
      {
        test: "/\.js\/",
        exclude: /(node_modules)/,
        use: {
            loader: "babel-loader",
            options: {
              presents: ["@babel/present-env"]
            }
          }
      },
      {
        test: /\.scss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          {
            loader: "css-loader"
          },
          {
            loader: "postcss-loader"
          },
          {
            loader: "sass-loader",
            options: {
                implementation: require('node-sass'),
              },
          }
        ]
      }
    ]
  },
};

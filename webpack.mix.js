let mix = require("laravel-mix")
let path = require("path")

mix.setResourceRoot("../")
mix.setPublicPath(path.resolve("./"))

mix.webpackConfig({
  watchOptions: {
    ignored: [path.posix.resolve(__dirname, "./node_modules"), path.posix.resolve(__dirname, "./css"), path.posix.resolve(__dirname, "./js")],
  },
  // 生產環境優化
  optimization: {
    splitChunks: {
      cacheGroups: {
        vendor: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendor',
          chunks: 'all',
        }
      }
    }
  },
  // 性能預算設定
  performance: {
    maxEntrypointSize: 250000,
    maxAssetSize: 250000
  }
})

// JavaScript 編譯與優化
mix.js("resources/js/app.js", "js")
  .minify("js/app.js") // 強制壓縮 JS

// CSS 編譯與優化 - 使用 postcss.config.js
mix.postCss("resources/css/app.css", "css")
  .postCss("resources/css/editor-style.css", "css")

// 生產環境額外優化
if (mix.inProduction()) {
  mix.version()
    .options({
      processCssUrls: false,
      terser: {
        terserOptions: {
          compress: {
            drop_console: true, // 移除 console.log
            drop_debugger: true, // 移除 debugger
            pure_funcs: ['console.log', 'console.info'] // 移除特定函數
          }
        }
      }
    })
} else {
  mix.options({
    manifest: false,
    processCssUrls: false
  })
}

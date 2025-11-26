module.exports = {
    plugins: [
        require('postcss-import'),
        require('tailwindcss/nesting'),
        require('tailwindcss'),
        require('autoprefixer'),
        // 只在生產環境中使用 cssnano 進行 CSS 壓縮和優化
        ...(process.env.NODE_ENV === 'production' ? [
            require('cssnano')({
                preset: ['default', {
                    autoprefixer: false, // 避免與 autoprefixer 衝突
                    cssDeclarationSorter: false, // 保持原有順序避免樣式問題
                    discardComments: { removeAll: true }, // 移除所有註釋
                    mergeRules: true, // 合併相同的規則
                    normalizeWhitespace: true, // 標準化空白
                    reduceIdents: false, // 保持 CSS 變數名稱
                    zindex: false, // 不優化 z-index 值
                }]
            })
        ] : [])
    ]
}

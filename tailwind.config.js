const theme = require("./theme.json")
const tailpress = require("@jeffreyvr/tailwindcss-tailpress")

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./**/*.php",
    "./resources/css/*.css",
    "./resources/js/*.js",
    "./safelist.txt"
  ],

  // 生產環境優化：移除未使用的樣式
  mode: 'jit', // Just-in-Time 編譯模式



  theme: {
    container: {
      padding: {
        DEFAULT: "1rem",
        sm: "2rem",
        lg: "0rem",
      },
    },
    extend: {
      colors: tailpress.colorMapper(tailpress.theme("settings.color.palette", theme)),
      fontSize: tailpress.fontSizeMapper(tailpress.theme("settings.typography.fontSizes", theme)),
    },
    screens: {
      xs: "480px",
      sm: "600px",
      md: "782px",
      lg: tailpress.theme("settings.layout.contentSize", theme),
      xl: tailpress.theme("settings.layout.wideSize", theme),
      "2xl": "1440px",
    },
    scrollbar: {
      DEFAULT: {
        track: "bg-gray-800",
        thumb: "bg-gray-600",
      },
      rounded: "rounded-md",
    },
  },

  // 優化插件載入
  plugins: [
    tailpress.tailwind,
    // require("@tailwindcss/line-clamp"), // 已內建於 Tailwind v3.3+，移除以避免警告
    require("tailwind-scrollbar"),
    function ({ addUtilities }) {
      addUtilities(
        {
          ".no-copy": {
            "user-select": "none",
          },
        },
        ["responsive", "hover"]
      )
    },
  ],

  // 生產環境 CSS 優化
  corePlugins: {
    // 如果不使用，可以禁用以下功能來減少 CSS 大小
    // preflight: false, // 禁用基礎樣式重置（謹慎使用）
  }
}

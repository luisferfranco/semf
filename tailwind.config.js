import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
     './storage/framework/views/*.php',
     './resources/**/*.blade.php',
     './resources/**/*.js',
     './resources/**/*.vue',
     "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [
    require("daisyui")
  ],
  daisyui: {
    themes: [
      {
        myLight: {
          "primary": "#f76923",
          "secondary": "#9333ea",
          "accent": "#ec4899",
          "neutral": "#8d8e99",
          "neutral-content": "#999",

          "error": "#dc2626",
          "warning": "#ffab00",
          "success": "#15803d",
          "info": "#2094f3",

          "base-100": "#fff",
          "base-content": "#333",
        },
        myDark: {
          "primary": "#ff9238",
          "secondary": "#d8b3ff",
          "accent": "#ffa2d5",
          "neutral": "#8d8e99",
          "neutral-content": "#cbd5e1",

          "error": "#ff6a6a",
          "warning": "#ffd54a",
          "success": "#29ff77",
          "info": "#7ad4ff",

          "base-100": "#64748b",
          "base-200": "#475569",
          "base-300": "#1e293b",
          "base-content": "#e7e5e4",
        }
      }
    ],
    base: true,
    styled: true,
  },
};

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
          "primary": "#3D71D9",
          "secondary": "#2b2d32",
          "success": "#1A7F4B",
          "error": "#D10E5C",
          "warning": "#FF9900",
          "accent": "#10a5ac",
          "neutral": "#8d8e99",
          "base-100": "#e7e5e4",
          "primary-content": "#333333",
          "neutral-content": "#555555",
        },
      },
      {
        myDark: {
          "primary": "#3D71D9",
          "secondary": "#2b2d32",
          "success": "#1A7F4B",
          "error": "#D10E5C",
          "warning": "#FF9900",
          "accent": "#10a5ac",
          "neutral": "#8d8e99",
          "base-100": "#334155",
          "primary-content": "#eeeeee",
          "neutral-content": "#aaaaaa",
        }
      },
    ],
    darkMode: "myDark",
    base: true,
    styled: true,
  },
};

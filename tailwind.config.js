/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./public/**/*.html",
    "./node_modules/preline/dist/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      colors: {
        brand: {
          dark: '#162345',
          accent: '#FBBF24',
          cream: '#FEFDF8',
          bg: '#333A4A'
        }
      }
    },
  },
  plugins: [
    require("@tailwindcss/forms")
  ],
  safelist: [
    // ✅ Preline HS Select states
    "hs-selected:block",
    "hs-selected:bg-select-item-active",
    "hs-selected:font-semibold",

    // ✅ Utility yang muncul di optionTemplate / extraMarkup
    "hidden",
    "block",
    "flex",
    "ms-auto",
    "me-2",
    "mt-1",
    "shrink-0",
    "size-4",
    "size-5",
    "text-primary",
    "rounded-full",
    "text-sm",
    "text-xs",
  ],
};

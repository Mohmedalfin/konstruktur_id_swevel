/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php", // Adjust these paths based on your framework structure
    "./public/**/*.html",
    "./src/**/*.{html,js,php}"
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
  plugins: [],
}

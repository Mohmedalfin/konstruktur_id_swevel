/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./public/**/*.html",
    "./node_modules/preline/dist/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: "var(--primary)",
        secondary: "var(--secondary)",

        bg: {
          landing1: "var(--bg-landing-1)",
          landing2: "var(--bg-landing-2)",
          nav: "var(--bg-nav)",
          third: "var(--bg-third)",
          dashboard: "var(--bg-dashboard)",
          card: "var(--bg-card)",
        },

        text: {
          primary: "var(--text-primary)",
          secondary: "var(--text-secondary)",
          pudar: "var(--text-pudar)",
          primaryPudar: "var(--text-primary-pudar)",
        },

        outline: {
          slider: "var(--outline-slider)",
        },

        checklist: {
          box: "var(--checklist-box)",
        },
      },
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
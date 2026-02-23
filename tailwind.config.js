/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./app/Views/**/*.php"],
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
  plugins: [],
};
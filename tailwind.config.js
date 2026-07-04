/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/Views/**/*.php', './app/Controllers/**/*.php'],
  // Nonaktifkan preflight agar tema vintage di custom.css tidak ter-reset
  corePlugins: {
    preflight: false,
  },
  theme: {
    extend: {
      colors: {
        cream: '#F4E9D8',
        sepia: '#8B5E3C',
        deep: '#4A3222',
        terracotta: '#C1502E',
        olive: '#7A8450',
        gold: '#C9A66B',
      },
      fontFamily: {
        heading: ['"Cormorant Garamond"', 'Georgia', 'serif'],
        body: ['Lora', 'Georgia', 'serif'],
      },
    },
  },
  plugins: [],
};

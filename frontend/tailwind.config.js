/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './pages/**/*.vue',
    './layouts/**/*.vue',
    './components/**/*.vue',
    '.app/vue',
    '@/assets/css/main.css'
  ],
  theme: {
    extend: {
      colors: {}
    },
  },
  plugins: [],
}


/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        arabic: ['Tajawal', 'sans-serif'],
      },
      colors: {
        primary: {
          50:  '#fdf8ee',
          100: '#faefd0',
          500: '#d4a017',
          600: '#b8860b',
          700: '#996b00',
        },
        dark: {
          800: '#1a1a2e',
          900: '#0f0f1a',
        }
      }
    },
  },
  plugins: [],
}

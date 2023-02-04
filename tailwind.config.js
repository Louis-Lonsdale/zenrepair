/** @type {import('tailwindcss').Config} */

const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
  content: ['./resources/views/**/*.{html,twig}'],
  theme: {
    extend: {
      fontFamily: {
        sans: ["'Source Sans Pro'", ...defaultTheme.fontFamily.sans],
        mono: ["'Source Code Pro'", ...defaultTheme.fontFamily.mono]
      },
    }
  },
  plugins: [
    require('@tailwindcss/forms')
  ],
}

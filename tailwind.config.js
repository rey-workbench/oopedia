/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.svelte",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#004e98',
          50: '#e6f2ff',
          100: '#cce5ff',
          200: '#99cbff',
          300: '#66b0ff',
          400: '#3396ff',
          500: '#004e98',
          600: '#003d75',
          700: '#002c52',
          800: '#001b2f',
          900: '#000a0c',
        },
        blue: {
          primary: '#004e98',
          secondary: '#0066cc',
          accent: '#3a8dff',
          dark: '#003a75',
          light: '#e6f2ff',
        },
      },
      backgroundImage: {
        'gradient-blue': 'linear-gradient(195deg, #0066cc 0%, #004e98 100%)',
        'gradient-primary': 'linear-gradient(195deg, #004e98, #003a75)',
        'gradient-cool': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'gradient-vibrant': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
        display: ['Poppins', 'Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}

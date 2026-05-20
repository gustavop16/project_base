/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        transparent: 'transparent',
        current: 'currentColor',
        primary: '#002D55',
        success: '#10b981',
        danger: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6',
        light: '#6b7280',
        dark: '#202020',
        foreground: '#292929'

      },
      transitionDuration: {
        
        '240': '240ms',
       }
    },
  },
  plugins: [],
}

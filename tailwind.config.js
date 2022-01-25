module.exports = {
  content: [
    "./app/views/**/**/*.phtml", 
    "./app/controllers/*.php", 
    "./web/js/*.js"
  ],
  theme: {
    extend: {
      animation: {
        'bounce': 'bounce 1s ease 0s 1 normal forwards',
        'smooth': 'smooth .3s ease-in-out',
        'smooth-out': 'smooth-out .3s ease-in-out',
        'fade': 'fade .3s ease-in-out',
        'fade-out': 'fade-out .3s ease-in-out',
      },
      keyframes: {
        'smooth': {
          '0%': {  opacity: 0, transform: 'scale(.95)' },
          '100%': { opacity: 1, transform: 'scale(1)' },
        },
        'smooth-out': {
          '0%': { opacity: 1, transform: 'scale(1)' },
          '100%': {  opacity: 0, transform: 'scale(.95)' },
        },
        'fade': {
          '0%': {  opacity: 0 },
          '100%': { opacity: 1 },
        },
        'fade-out': {
          '0%': { opacity: 1 },
          '100%': {  opacity: 0 },
        }
      }
    },
    fontFamily: {
      'catamaran': ['"Catamaran"', 'sans-serif']
    },
    screens: {
      'vsm': {'min': '320px'},
      'sm': {'min': '640px'},
      'md': {'min': '768px'},
      'lg': {'min': '1024px'},
      'xl': {'min': '1280px'},
      '2xl': {'min': '1536px'},
      'vsm-max': {'max': '389px'},
      'sm-max': {'max': '656px'},
      'md-max': {'max': '768px'},
      'lg-max': {'max': '1024px'},
      'xl-max': {'max': '1280px'},
      '2xl-max': {'max': '1536px'},
    }
  },
  plugins: [require('@tailwindcss/forms')],
}

// * Usage for production
// curl -sLO https://github.com/tailwindlabs/tailwindcss/releases/latest/download/tailwindcss-windows-x64.exe
// mv tailwindcss-windows-x64.exe tailwindcss.exe
// tailwindcss -i .\tailwind-input.css -o ./web/css/tailwind.css --minify
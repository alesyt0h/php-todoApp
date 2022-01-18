module.exports = {
  content: ["./app/views/**/**/*.phtml", "./app/controllers/*.php", "./web/js/*.js"],
  theme: {
    extend: {},
  },
  plugins: [],
}

// * Usage for production
// curl -sLO https://github.com/tailwindlabs/tailwindcss/releases/latest/download/tailwindcss-windows-x64.exe
// mv tailwindcss-windows-x64.exe tailwindcss.exe
// tailwindcss -i .\tailwind-input.css -o ./web/css/tailwind.css --minify
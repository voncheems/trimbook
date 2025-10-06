/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",        // ✅ catches index.php in root
    "./src/**/*.php", // optional: if you add PHP files later in src
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};

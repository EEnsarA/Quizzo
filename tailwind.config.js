// tailwind.config.js
//** {import('tailwindcss').Config} */

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                'custom-sans': ['Instrument Sans', 'sans-serif'],
                'major-mono': ['Major Mono Display', 'mono'],
            },
        },
    },
    plugins: [],
}
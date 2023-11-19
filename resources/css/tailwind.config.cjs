const colors = require("tailwindcss/colors");

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "resources/views/**/*.blade.php",
        "resources/js/**/*.js",
    ],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                primary: colors.blue,
                success: colors.green,
                warning: colors.amber,
            },
        },
    },
    corePlugins: {
        preflight: false,
    },
    plugins: [
        require('@tailwindcss/typography')
    ],
    safelist: [
        //
    ],
};

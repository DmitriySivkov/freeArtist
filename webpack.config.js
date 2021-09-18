const path = require('path');

module.exports = {
    resolve: {
        alias: {
            Root: path.resolve(__dirname, "resources/js/"),
            RootSass: path.resolve(__dirname, "resources/sass/"),
        }
    },
};

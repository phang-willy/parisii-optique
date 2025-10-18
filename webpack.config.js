const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
        'blocks/index': './blocks/index.js',
    },
    output: {
        path: __dirname + '/blocks/dist',
        filename: '[name].js',
    },
};

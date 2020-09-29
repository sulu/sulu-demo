/* eslint-disable flowtype/require-valid-file-annotation */
/* eslint-disable import/no-nodejs-modules*/
/* eslint-disable no-undef */
const path = require('path');
const webpackConfig = require('../../vendor/sulu/sulu/webpack.config.js');

module.exports = (env, argv) => {
    env = env ? env : {};
    argv = argv ? argv : {};

    env.project_root_path = path.resolve(__dirname, '..', '..');
    env.node_modules_path = path.resolve(__dirname, 'node_modules');

    const config = webpackConfig(env, argv);
    config.entry = path.resolve(__dirname, 'index.js');

    const suluPath = path.resolve(__dirname, '../../vendor/sulu/sulu/');
    const loginScssPath = path.resolve(suluPath, 'src/Sulu/Bundle/AdminBundle/Resources/js/containers/Login/login.scss');

    /* overwrite stylesheet of sulu login component with custom stylesheet of project */
    config.resolve.alias[loginScssPath] = path.resolve(__dirname, 'style-overwrites/login.scss');

    return config;
};

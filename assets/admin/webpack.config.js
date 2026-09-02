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

    /* overwrite stylesheet of sulu login component with custom stylesheet of project */
    const loginScssPath = path.resolve(env.node_modules_path, 'sulu-admin-bundle/containers/Login/login.scss');
    config.resolve.alias[loginScssPath] = path.resolve(__dirname, 'style-overwrites/login.scss');

    return config;
};

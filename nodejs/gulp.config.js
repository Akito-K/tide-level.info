const path = require('path');

/* プロジェクト設定 ここから */
const mode       = 'development';
const port       = 60002; // ←docker-composeで指定したport
const projectDir = path.resolve(__dirname, '../');
const distDir    = projectDir + '/root/public_html/shared';
const cssDistDir = distDir + '/css/dest';
const jsDistDir  = distDir + '/js/dest';
const scssDir    = './scss';
const tsDir      = './ts';

const scssSources = [
    scssDir + '/*.scss',
    scssDir + '/*/*.scss',
    scssDir + '/*/*/*.scss',
];
const tsSources = [
    tsDir + '/*.ts',
    tsDir + '/*/*.ts',
    tsDir + '/*/*/*.ts',
];
/* プロジェクト設定 ここまで */

const config = {
    mode : mode,
    port : port,
    cssDistDir : cssDistDir,
    jsDistDir : jsDistDir,
    scssSources : scssSources,
    tsSources : tsSources,
    tsDir : tsDir,
};
module.exports = config;
/**
 * 
 * 项目配置详情
*/

const conf = require('./config/projectConfig');
module.exports = {
  pages: conf.pages,
  productionSourceMap: false, // 生产环境是否生成 sourceMap 文件，一般情况不建议打开
  lintOnSave: false,
  publicPath: conf.pages.index.publicPath, // 基本路径
  outputDir: conf.pages.index.outputDir
};

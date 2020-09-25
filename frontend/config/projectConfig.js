const projectName = require('./project')

const config = {
  //project 1
  develop:{
      localPath:'./src/projects/develop/',  //项目相对目录
      rkPath:'/dist/develop/', //项目文件目录
      buildFilePath:'../dist/develop/', //打包输出的目录名和相对路径
      rootPath:'/cloudnetlot/frontend/' //前端相对的根目录（固定目录）
  },
  //project 2
  admin:{
      localPath:'./src/projects/admin/',  //项目相对目录
      rkPath:'/dist/admin/', //项目文件目录
      buildFilePath:'../dist/admin/', //打包输出的目录名和相对路径
      rootPath:'/cloudnetlot/frontend/' //前端相对的根目录（固定目录）
  },
  //project 3
  home:{
    localPath:'./src/projects/home/',  //项目相对目录
    rkPath:'/dist/home/', //项目文件目录
    buildFilePath:'../dist/home/', //打包输出的目录名和相对路径
    rootPath:'/cloudnetlot/frontend/'//前端相对的根目录（固定目录）
  },
}
const configObj = config[projectName.name]
module.exports = configObj
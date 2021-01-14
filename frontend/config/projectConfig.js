const projectName = require('./project')
const config = {
    //项目1
    home: {
		pages: {
			index: {
				publicPath: './', // 基本路径
				entry: 'src/projects/home/main.js',
				outputDir: "dist/home",
				//assetsDir: './assets',
				envPath:'../env.js',
				template: 'public/index.html',
				filename: 'index.html',
			}	
		}
	},
	//项目2
	develop: {
		pages: {
			index: {
				publicPath: './', // 基本路径
				entry: 'src/projects/develop/main.js',
				outputDir: "dist/develop/",
				//assetsDir: './assets',
				envPath:'../env.js',
				template: 'public/index.html',
				filename: 'index.html',
			}
		}
	},
	//项目3
	admin: {
		pages: {
			index: {
				publicPath: './', // 基本路径
				entry: 'src/projects/admin/main.js',
				outputDir: "dist/admin/",
				//assetsDir: './assets',
				envPath:'../env.js',
				template: 'public/index.html',
				filename: 'index.html',
			}
		}
	}
}

const configObj = config[projectName.name]
module.exports = configObj

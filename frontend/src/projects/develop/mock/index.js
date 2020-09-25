import Mock from 'mockjs'

import authApi from './auth'
import userAPI from './user'
import productAPI from './product'

// Fix an issue with setting withCredentials = true, cross-domain request lost cookies
// https://github.com/nuysoft/Mock/issues/300
Mock.XHR.prototype.proxy_send = Mock.XHR.prototype.send
Mock.XHR.prototype.send = function() {
  if (this.custom.xhr) {
    this.custom.xhr.withCredentials = this.withCredentials || false
  }
  this.proxy_send(...arguments)
}
// Mock.setup({
//   timeout: '350-600'
// })

//auth /
Mock.mock(/\/backend\/develop\/auth\/token/, 'post', authApi.token)
Mock.mock(/\/backend\/develop\/auth\/token\/refresh/, 'post', authApi.refresh)
Mock.mock(/\/backend\/develop\/auth\/token\/destroy/, 'get', authApi.destroy)

//user
Mock.mock(/\/backend\/develop\/user\/info/, 'get', userAPI.userinfo)
Mock.mock(/\/backend\/develop\/user\/develop/, 'post', userAPI.develop)

//product
Mock.mock(/\/backend\/develop\/product\/list/, 'post', productAPI.list)
Mock.mock(/\/backend\/develop\/product\/register/, 'post', productAPI.add)
Mock.mock(/\/backend\/develop\/product\/save/, 'post', productAPI.edit)
Mock.mock(/\/backend\/develop\/product\/info/, 'get', productAPI.infos)
Mock.mock(/\/backend\/develop\/product\/delete/, 'post', productAPI.del)
Mock.mock(/\/backend\/develop\/product\/publish/, 'post', productAPI.publish)


export default Mock

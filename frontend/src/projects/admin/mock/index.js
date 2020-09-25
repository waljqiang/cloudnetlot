import Mock from 'mockjs'

import authApi from './auth'
import userAPI from './user'
import developAPI from './develop'
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

//auth  /
Mock.mock(/\/backend\/admin\/auth\/token/, 'post', authApi.token)
Mock.mock(/\/backend\/admin\/auth\/token\/refresh/, 'post', authApi.refresh)
Mock.mock(/\/backend\/admin\/auth\/token\/destroy/, 'get', authApi.destroy)

//user
Mock.mock(/\/backend\/admin\/user\/info/, 'get', userAPI.userinfo)

//develop
Mock.mock(/\/backend\/admin\/develop\/list/, 'post', developAPI.list)
Mock.mock(/\/backend\/admin\/develop\/approve/, 'post', developAPI.approve)
Mock.mock(/\/backend\/admin\/develop\/info/, 'post', developAPI.info)

//product
Mock.mock(/\/backend\/admin\/product\/list/, 'post', productAPI.list)
Mock.mock(/\/backend\/admin\/product\/info/, 'get', productAPI.info)
Mock.mock(/\/backend\/admin\/product\/approve/, 'post', productAPI.audit)

export default Mock

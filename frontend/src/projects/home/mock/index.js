import Mock from 'mockjs'

import authApi from './auth'
import userAPI from './user'
import systemAPI from './system'

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

//auth 
Mock.mock(/\/backend\/auth\/token/, 'post', authApi.token)
Mock.mock(/\/backend\/auth\/token\/refresh/, 'post', authApi.refresh)
Mock.mock(/\/backend\/auth\/token\/destroy/, 'get', authApi.destroy)

//user
Mock.mock(/\/backend\/user\/info/, 'get', userAPI.userinfo)
Mock.mock(/\/backend\/user\/register/, 'post', userAPI.register)

// system
Mock.mock(/\/backend\/api\/system\/countrycode/, 'get', systemAPI.countrycode)
// Mock.mock(/\/auth\/token/, 'post', userAPI.token)/
// Mock.mock(/\/User\/info/, 'get', userAPI.getInfo)
// Mock.mock(/\/User\/loginout/, 'post', userAPI.logout)
// Mock.mock(/\/User\/menus/, 'get', userAPI.userFun)

// // account
// Mock.mock(/\/System\/menus/, 'get', systemAPI.getMenu)
// Mock.mock(/\/System\/func/, 'get', systemAPI.getFun)
// Mock.mock(/\/System\/passwordemail/, 'post', systemAPI.sendmail)
// Mock.mock(/\/System\/emailmodifypassword/, 'post', systemAPI.resetPwd)

// // Role
// Mock.mock(/\/Role\/list/, 'get', roleAPI.list)
// Mock.mock(/\/Role\/info/, 'get', roleAPI.info)
// Mock.mock(/\/Role\/add/, 'post',roleAPI.add)
// Mock.mock(/\/Role\/save/, 'post',roleAPI.save)
// Mock.mock(/\/Role\/delete/, 'post',roleAPI.delete)
// Mock.mock(/\/Role\/distribute/, 'post',roleAPI.distribute)

// //user-account
// Mock.mock(/\/User\/sublist/, 'get', userAPI.sublist)
// Mock.mock(/\/User\/subinfo/, 'get', userAPI.subinfo)
// Mock.mock(/\/User\/registersub/, 'post', userAPI.addSub)
// Mock.mock(/\/User\/savesub/, 'post', userAPI.saveSub)
// Mock.mock(/\/User\/resetsubpassword/, 'post', userAPI.resetPwd)
// Mock.mock(/\/User\/deletesub/, 'post', userAPI.delAccount)
// Mock.mock(/\/User\/save/, 'post', userAPI.saveInfos)
// Mock.mock(/\/User\/savepassword/, 'post', userAPI.savePwd)
// Mock.mock(/\/User\/setemailserver/, 'post', userAPI.saveMailServer)
// Mock.mock(/\/User\/getemailserver/, 'get', userAPI.loadMailServer)
// Mock.mock(/\/User\/testemailserver/, 'post', userAPI.testMailServer)

// //tree
// Mock.mock(/\/Tree\/all/, 'get', treeAPI.getTeeAll)
// Mock.mock(/\/Tree\/child/, 'get', treeAPI.getChild)
// Mock.mock(/\/Tree\/info/, 'get', treeAPI.getNodeInfo)
// Mock.mock(/\/Tree\/delete/, 'get', treeAPI.delTree)
// Mock.mock(/\/Tree\/add/, 'post', treeAPI.addTree)
// Mock.mock(/\/Tree\/save/, 'post', treeAPI.saveTree)
// Mock.mock(/\/Tree\/devcounts/, 'post', treeAPI.devcounts)

// //device
// Mock.mock(/\/Device\/list/, 'post', deviceAPI.getList)
// Mock.mock(/\/Device\/count/, 'get', deviceAPI.getCount)
// Mock.mock(/\/Device\/info/, 'post', deviceAPI.getDevInfo)
// Mock.mock(/\/Device\/setname/, 'post', deviceAPI.saveName)
// Mock.mock(/\/Device\/fun/, 'get', deviceAPI.getDevFun)
// Mock.mock(/\/Device\/getwifi/, 'post', deviceAPI.getwifi)
// Mock.mock(/\/Device\/getwifioptions/, 'post', deviceAPI.wifioptions)
// Mock.mock(/\/Device\/gettimewifi/, 'get', deviceAPI.gettimewifi)
// Mock.mock(/\/Device\/setwifi/, 'post', deviceAPI.saveWifi)
// Mock.mock(/\/Device\/settimewifi/, 'post', deviceAPI.saveWifiTimer)
// Mock.mock(/\/Device\/gettimereboot/, 'get', deviceAPI.gettimereboot)
// Mock.mock(/\/Device\/settimereboot/, 'post', deviceAPI.settimereboot)
// Mock.mock(/\/Device\/reboot/, 'post', deviceAPI.devReboot)
// Mock.mock(/\/Device\/reboots/, 'post', deviceAPI.batch_reboot)
// Mock.mock(/\/Device\/deletes/, 'post', deviceAPI.batch_del)
// Mock.mock(/\/Device\/getremote/, 'get', deviceAPI.getremote)
// Mock.mock(/\/Device\/setwifipowers/, 'post', deviceAPI.setPower)
// Mock.mock(/\/Device\/setwifis/, 'post', deviceAPI.setBatchWifi)
// Mock.mock(/\/Device\/setnode/, 'post', deviceAPI.setDevItem)
// Mock.mock(/\/Device\/getrepeater/, 'get', deviceAPI.getBrInfo)
// Mock.mock(/\/Device\/setrepeater/, 'post', deviceAPI.setBr)
// Mock.mock(/\/Device\/getdeveqtype/, 'get', deviceAPI.typeList)
// Mock.mock(/\/Device\/getupgradepack/, 'get', deviceAPI.versionList)
// Mock.mock(/\/Device\/delupgradepack/, 'get', deviceAPI.delVerList)
// Mock.mock(/\/Device\/downloads/, 'post', deviceAPI.devDownLoad)
// Mock.mock(/\/Device\/upgradehistory/, 'get', deviceAPI.upgradehistory)
// Mock.mock(/\/Device\/delupgradeorders/, 'post', deviceAPI.delhistory)
// Mock.mock(/\/Device\/upgradeinfo/, 'get', deviceAPI.historyinfos)
// Mock.mock(/\/Device\/deldevfromupgradeorder/, 'post', deviceAPI.delDowDev)
// Mock.mock(/\/Device\/redownloads/, 'post', deviceAPI.redownloads)


// //topo
// Mock.mock(/\/Topography\/infos/, 'get', topoAPI.getTopo)
// Mock.mock(/\/Topography\/init/, 'get', topoAPI.getInit)
// Mock.mock(/\/Topography\/topstateevent/, 'get', topoAPI.getTopoNodeInfo)
// Mock.mock(/\/Device\/getotherid/, 'get', topoAPI.getOtherId)
// Mock.mock(/\/Topography\/rebuild/, 'get', topoAPI.resetTopo)
// Mock.mock(/\/Topography\/save/, 'post', topoAPI.saveTopoData)
export default Mock

import request from '@/utils/request'

export function devList(params) {
  return request({
    url: '/backend/device/list',
    method: 'post',
    data:params
  })
}

export function userActivity(params) {
  return request({
    url: '/backend/device/statistics/clients/onlines',
    method: 'post',
    data:params
  })
}

export function devStatistics(params) {
  return request({
    url: '/backend/device/statistics',
    method: 'get',
    params
  })
}

export function devInfos(params) {
  return request({
    url: '/backend/device/infos',
    method: 'post',
    data:params
  })
}

export function refresh(params) {
  return request({
    url: '/backend/device/reports',
    method: 'post',
    data:params
  })
}

export function setname(params) {
  return request({
    url: '/backend/device/setname',
    method: 'post',
    data:params
  })
}

export function reboot(params){
  return request({
    url: '/backend/device/restart',
    method: 'post',
    data:params
  })
}

export function timedReboot(params){
  return request({
    url: '/backend/device/timerestart',
    method: 'post',
    data:params
  })
}

export function deletes(params){
  return request({
    url: '/backend/device/deletes',
    method: 'post',
    data:params
  })
}

export function wifiOptions(params){
  return request({
    url: '/backend/device/getwifioptions',
    method: 'post',
    data:params
  })
}

export function wifiInfos(params){
  return request({
    url: '/backend/device/wifi/info',
    method: 'post',
    data:params
  })
}

export function setWifi(params){
  return request({
    url: '/backend/device/setwifi',
    method: 'post',
    data:params
  })
}

export function setDevItem(params){
  return request({
    url: '/backend/device/transgroup',
    method: 'post',
    data:params
  })
}

export function batchWifi(params){
  return request({
    url: '/backend/device/setwifis',
    method: 'post',
    data:params
  })
}







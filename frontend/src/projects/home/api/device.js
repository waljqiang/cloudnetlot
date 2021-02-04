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




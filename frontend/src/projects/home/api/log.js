import request from '@/utils/request'

export function oplogStatics(params) {
  return request({
    url: 'backend/oplog/statistics',
    method: 'get',
    params
  })
}

export function oplogList(params) {
  return request({
    url: 'backend/oplog/list',
    method: 'post',
    data:params
  })
}

export function getinfo(params) {
  return request({
    url: 'backend/oplog/info',
    method: 'get',
    params
  })
}

export function setRead(params) {
  return request({
    url: 'backend/oplog/readed',
    method: 'post',
    data:params
  })
}



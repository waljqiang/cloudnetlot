import request from '@/utils/request'

export function getTreeData(params) {
  return request({
    url: '/backend/workgroup/all',
    method: 'get',
    params
  })
}

export function treeAll(params) {
  return request({
    url: '/Tree/all',
    method: 'get',
    params
  })
}

export function getNodeInfo(params) {
  return request({
    url: '/backend/workgroup/info',
    method: 'get',
    params
  })
}

export function delNode(params) {
  return request({
    url: '/backend/workgroup/delete',
    method: 'get',
    params
  })
}

export function addTree(params) {
  return request({
    url: '/backend/workgroup/add',
    method: 'post',
    data:params
  })
}

export function saveTree(params) {
  return request({
    url: '/backend/workgroup/save',
    method: 'post',
    data:params
  })
}

export function download(params) {
  return request({
      url: '/backend/workgroup/download/config',
      method: 'get',
      params
  })
}

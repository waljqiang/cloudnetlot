import request from '@/utils/request'

export function getUserInfo(params) {
  return request({
    url: 'backend/admin/user/info',
    method: 'get',
    params
  })
}


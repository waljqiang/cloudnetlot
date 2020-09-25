import request from '@/utils/request'

export function getUserInfo(params) {
  return request({
    url: 'backend/user/info',
    method: 'get',
    params
  })
}

export function register(params) {
  return request({
    url: 'backend/user/register',
    method: 'post',
    data:params
  })
}



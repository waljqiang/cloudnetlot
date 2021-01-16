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

export function editUserInfo(params) {
  return request({
    url: 'backend/user/save',
    method: 'post',
    data:params
  })
}

export function editUserPwd(params) {
  return request({
    url: 'backend/user/password/save',
    method: 'post',
    data:params
  })
}

export function sendEmail(params) {
  return request({
    url: 'backend/user/password/sendmail',
    method: 'post',
    data:params
  })
}

export function checkmail(params) {
  return request({
    url: 'backend/user/password/checkmail',
    method: 'post',
    data:params
  })
}

export function resetPwd(params) {
  return request({
    url: 'backend/user/password/reset',
    method: 'post',
    data:params
  })
}

export function userList(params) {
  return request({
    url: 'backend/user/child/list',
    method: 'post',
    data:params
  })
}

export function userCount(params) {
  return request({
    url: 'backend/user/child/count',
    method: 'get',
    params
  })
}



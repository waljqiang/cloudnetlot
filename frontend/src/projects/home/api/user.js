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

export function userAdd(params) {
  return request({
    url: 'backend/user/child/add',
    method: 'post',
    data:params
  })
}

export function userEdit(params) {
  return request({
    url: 'backend/user/child/save',
    method: 'post',
    data:params
  })
}

export function childInfo(params) {
  return request({
    url: 'backend/user/child/info',
    method: 'get',
    params
  })
}

export function resetChildPwd(params) {
  return request({
    url: 'backend/user/child/resetspassword',
    method: 'post',
    data:params
  })
}

export function delChild(params) {
  return request({
    url: 'backend/user/child/deletes',
    method: 'post',
    data:params
  })
}







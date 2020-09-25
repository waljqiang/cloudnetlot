import request from '@/utils/request'

export function login(account, password) {
  return request({ 
    url: 'backend/auth/token',
    method: 'post',
    data: {
      username:account,
      password:password
    }
  })
}

export function refreshToken(refresh_token,token) {
  return request({
    url: 'backend/auth/token/refresh',
    method: 'post',
    data: {
      refresh_token:refresh_token
    }
  })
}


export function logout() {//destroy token
  return request({
    url: 'backend/auth/token/destroy',
    method: 'get'
  })
}

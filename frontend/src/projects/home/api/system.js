import request from '@/utils/request'

export function getCountrycode(params) {
  return request({
    url: 'backend/api/system/countrycode',
    method: 'get',
    params
  })
}


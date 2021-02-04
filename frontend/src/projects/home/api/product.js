import request from '@/utils/request'

export function statistics(params) {
  return request({
    url: '/backend/product/statisticswithdevices',
    method: 'get',
    params
  })
}



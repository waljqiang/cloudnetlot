import request from '@/utils/request'

export function productList(params) {
	return request({
		url: 'backend/admin/develop/product/list',
		method: 'post',
		data:params
	})
}

export function auditRequest(params) {
	return request({
		url: 'backend/admin/develop/product/approve',
		method: 'post',
		data:params
	})
}

export function productInfo(params) {
	return request({
		url: 'backend/admin/develop/product/info',
		method: 'get',
		params
	})
}









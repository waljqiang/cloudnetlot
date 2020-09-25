import request from '@/utils/request'

export function developtList(params) {
	return request({
		url: 'backend/admin/develop/list',
		method: 'post',
		data:params
	})
}

export function developInfo(params) {
	return request({
		url: 'backend/admin/develop/info',
		method: 'post',
		data:params
		
	})
}

export function auditRequest(params) {
	return request({
		url: 'backend/admin/develop/approve',
		method: 'post',
		data:params	
	})
}






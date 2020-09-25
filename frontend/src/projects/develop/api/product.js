import request from '@/utils/request'

export function productList(params) {
	return request({
		url: 'backend/develop/product/list',
		method: 'post',
		data:params
	})
}

export function addProduct(params) {
	return request({
		url: 'backend/develop/product/register',
		method: 'post',
		data:params
		
	})
}

export function editProduct(params) {
	return request({
		url: 'backend/develop/product/save',
		method: 'post',
		data:params
		
	})
}

export function infoProduct(params) {
	return request({
		url: 'backend/develop/product/info',
		method: 'get',
		params
		
	})
}

export function delProduct(params) {
	return request({
		url: 'backend/develop/product/delete',
		method: 'post',
		data:params	
	})
}

export function publish(params){
	return request({
		url:'backend/develop/product/publish',
		method:'post',
		data:params
	})
}




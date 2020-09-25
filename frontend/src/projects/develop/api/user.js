import request from '@/utils/request'

export function getUserInfo(params) {
	return request({
		url: 'backend/develop/user/info',
		method: 'get',
		params
	})
}

export function develop(parObj) {
	return request({
		url: 'backend/develop/user/develop',
		method: 'post',
		data: {
			name:parObj.name,
			idcard:parObj.idCard,
			enterprise:parObj.enterprise,
			enterprise_des:parObj.enterpriseDesc,
			enterprisecode:parObj.enterpriseCode,
			lang:parObj.lang
		}
	})
}




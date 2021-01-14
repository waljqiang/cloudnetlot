export default {
	userinfo: res => {
		return {
			"status": 10000,
			"data": {
				"uid": "cnlypawyozqpverlbwjknbmdxagn",
				"username": "waljqiang",
				"nickname": "万年路口",
				"pid": "cnlypxmdenbwgzkjyejyqxpvraob",
				"is_primary": false,
				"email": "1454968008@qq.com",
				"phonecode": "86",
				"phone": "13468826446",
				"level": 1,
				"area": 0,
				"address": "锦业路",
				"longitude": "",
				"latitude": "",
				"status": 1,
				"admin_id": "cnlwmnaxqkpembgjvmlyonzvrwdz",
				"timeZone": "+08:00",
				"isSummerTime": 0,
				"created_at": "2020-06-22 14:26:19",
				"updated_at": "2020-06-23 11:13:49"
			},
			"errorCode": [600000100]
		}
	},
	register: res => {
		return {
			"status": 100001,
			"data": {
				 "uid": "cnlngqzpqybvodrlxajemkwagnxy"
			},
			"errorCode": [600400110]
	   	}
	},
	editInfo: res => {
		return {
			"status": 10000,
			"data": [],
			"message": "Invalid parameters",
			"errorCode": [
				 600400100
			]
	   	}
	},
	editPwd: res => {
		return {
			"status": 10000,
			"data": [],
			"message": "Invalid parameters",
			"errorCode": [
				 600400100
			]
	   	}
	},
	sendEmail: res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
	   	}
	},
	resetPwd: res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
	   	}
	},
	
}

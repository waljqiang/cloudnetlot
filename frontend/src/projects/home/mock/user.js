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
	userCount: res => {
		return {
			"status": 10000,
			"data": {
				 "all": 2,
				 "enabled": 2,
				 "disabled": 0
			},
			"errorCode": []
	   }
	},
	userList: res => {
		return {
			"status": 10000,
			"data": {
				 "total": 2,
				 "list": [
					  {
						   "uid": "cnlpmxqgzaprmbvjqyjyoxkenwdo",
						   "username": "waljqiang_2",
						   "nickname": "万年路口子账号2",
						   "pid": "cnlngqzpqybvodrlxajemkwagnxy",
						   "email": "liuzq@yuncore.com.cn",
						   "phonecode": "86",
						   "phone": "13468826445",
						   "level": 2,
						   "status": 1,
						   "created_at": "2020-06-29 16:10:36",
						   "updated_at": "2020-06-29 16:10:36"
					  },
					  {
						   "uid": "cnlapnvenbyqzwdlmxjmxgaoprkz",
						   "username": "waljqiang_1",
						   "nickname": "万年路口子账号1",
						   "pid": "cnlngqzpqybvodrlxajemkwagnxy",
						   "email": "liuzq@yuncore.com.cn",
						   "phonecode": "86",
						   "phone": "13468826445",
						   "level": 2,
						   "status": 1,
						   "created_at": "2020-06-29 11:43:45",
						   "updated_at": "2020-06-29 11:43:45"
					  }
				 ]
			},
			"errorCode": []
	   	}
	},
}

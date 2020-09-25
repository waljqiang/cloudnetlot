export default {
	list: res => {
		return {
			"status": 10000,
			"data": {
				"total": 1,
				"list": [
					{
						"user_id": "cnlngqzpqybvodrlxajemkwagnxy",
						"username": "cloud",
						"nickname": "云平台开发者",
						"email": "11@11.com",
						"phonecode": "86",
						"phone": "13468826445",
						"name": "云平台开发者",
						"enterprise": "系统",
						"appid": "",
						"secret": "",
						"status": 1,
						"created_at": "2020-07-22 18:10:57"
					}
				]
			},
			"errorCode": []
	   	}
	},
	approve:res => {
		return {
			"status": 10000,
			"data": {
				 "appid": "658c8707cdcf5ac6a4437fc427f55bcb",
				 "secret": "ZlGaLSM4rXn4JJBihBG2BUa0yz2tRwuziCn0mFtD"
			},
			"errorCode": []
	   	}
	},
	info:res => {
		return {
			"status": 10000,
			"data": {
				 "uid": "cnlngqzpqybvodrlxajemkwagnxy",
				 "username": "cloud",
				 "nickname": "云平台开发者",
				 "email": "11@11.com",
				 "phonecode": "86",
				 "phone": "13468826445",
				 "timeZone": "+08:00",
				 "isSummerTime": 0,
				 "name": "云平台开发者",
				 "idcard": "631456199007157245",
				 "enterprise": "系统",
				 "enterprise_des": "云平台自身研发",
				 "enterprisecode": "12322434654663aa",
				 "appid": "",
				 "secret": "",
				 "status": 1,
				 "created_at": "2020-07-23 15:37:56"
			},
			"errorCode": []
	   	}
	}
}

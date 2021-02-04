export default {
	userinfo: res => {
		return {
			"status":10000,
			"data":{
				"uid":"cnlapnvenbyqzwdlmxjmxgaoprkz",
				"username":"test001",
				"nickname":"lianglibo",
				"pid":"cnlypxmdenbwgzkjyejyqxpvraob",
				"is_primary":true,
				"email":"303098530@qq.com",
				"phonecode":"86",
				"phone":"18792445299",
				"level":3,
				"area":0,
				"address":"科技二路",
				"longitude":"",
				"latitude":"",
				"status":1,
				"admin_id":"cnlwmnaxqkpembgjvmlyonzvrwdz",
				"timeZone":"+08:00",
				"isSummerTime":0,
				"created_at":"2021-01-13 13:56:14",
				"updated_at":"2021-01-19 11:02:02"
			},
			"errorCode":[
		
			]
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
						   "status": 0,
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
						   "status": 0,
						   "created_at": "2020-06-29 11:43:45",
						   "updated_at": "2020-06-29 11:43:45"
					  }
				 ]
			},
			"errorCode": []
	   	}
	},
	userAdd: res => {
		return {
			"status": 10000,
			"data": {
				 "uid": "cnlzrwmzexrpvkojdglynwqabgdq"
			},
			"errorCode": []
	   }
	},
	childInfo: res => {
		return {
			"status": 10000,
			"data": {
				 "uid": "cnlpmxqgzaprmbvjqyjyoxkenwdo",
				 "username": "waljqiang_1",
				 "nickname": "修改子账号信息测试",
				 "pid": "cnlngqzpqybvodrlxajemkwagnxy",
				 "phonecode": "86",
				 "phone": "13468826445",
				 "email": "liuzq@yuncore.com.cn",
				 "level": '1',
				 "status": '1',
				 "gids": [
					  "cnlngqzpqybvodrlxajemkwagnxy"
				 ],
				 "created_at": "2021-01-15 13:49:35"
			},
			"errorCode": []
	   	}
	},
	editBack: res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
	   }
	},
	resetChildPwd: res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
	   	}
	},
	delChild: res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
	   	}
	},
}

export default {
	statics: res => {
		return {
			"status": 10000,
			"data": {
				 "all": 6,
				 "reads": 1,
				 "unreads": 5
			},
			"errorCode": []
	   }
	},
	info: res => {
		return {
			"status": 10000,
			"data": {
				"mac": "20:05:05:60:32:6C",
				"ip": "192.168.111.39",
				"dev_type": "FAC7000",
				"name": "AC测试",
				"status": 3,
				"type": 6,
				"version": "FAC7000B-GW-V6.0-B20200103171044",
				"created_at": "2021-01-14 13:41:49"
			},
			"errorCode": []
		}
	},
	read: res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
	   }
	},
	list: res => {
		return {
			"status": 10000,
			"data": {
				"total": 1,
				"list": [
					{
						"id": "cnlzrwmzexrpvkojdglynwqabgdq",
						"user_id": "cnlngqzpqybvodrlxajemkwagnxy",
						"username": "cloud",
						"nickname": "万年路口",
						"dev_mac": "20:05:05:60:32:6C",
						"common_id": "000200081610532501",
						"content": {
							"header": {
									"protocol": "v1.0",
									"type": "2",
									"encode": "1",
									"mid": "GS8Z3P1610532502"
							},
							"body": {
									"comm_id": "000200081610532501",
									"command": {
										"type": "bind",
										"bind": "ABMCBQAABgUFAgVxBFMTBBMEAAEDAQNwAwUF",
										"token": "356a192b7913b04c54574d18c28d46e6395428ab"
									}
							},
							"now": 1610532501
						},
						"describe": "",
						"type": 10,
						"status": 3,
						"readed": 0,
						"created_at": "2021-01-13 18:08:21"
					},
					{
						"id": "cnlzrwmzexrpvkojdglynwqabgdq",
						"user_id": "cnlngqzpqybvodrlxajemkwagnxy",
						"username": "cloud",
						"nickname": "万年路口",
						"dev_mac": "20:05:05:60:32:6C",
						"common_id": "000200081610532501",
						"content": {
							"header": {
									"protocol": "v1.0",
									"type": "2",
									"encode": "1",
									"mid": "GS8Z3P1610532502"
							},
							"body": {
									"comm_id": "000200081610532501",
									"command": {
										"type": "bind",
										"bind": "ABMCBQAABgUFAgVxBFMTBBMEAAEDAQNwAwUF",
										"token": "356a192b7913b04c54574d18c28d46e6395428ab"
									}
							},
							"now": 1610532501
						},
						"describe": "",
						"type": 8,
						"status": 3,
						"readed": 0,
						"created_at": "2021-01-13 18:08:21"
					}
				]
			},
			"errorCode": []
	   	}
	},
	
}

export default {
	list: res => {
		return {
			"status": 10000,
			"data": {
				"total": 1,
				"list": [
					  {
						   "prtid": "prtzvxejpyvzeyvdvmalnyrdb",
						   "uid": "cnlngqzpqybvodrlxajemkwagnxy",
						   "name": "产品测试",
						   "type": 1,
						   "size": "CPE200",
						   "status": 1,
						   "created_at": "2020-07-27 11:43:43"
					  }
				]
			},
			"errorCode": []
	   	}
	},
	add : res => {
		return {
			"status": 10000,
			"data": {
				 "prtid": "prtzvxejpyvzeyvdvmalnyrdb"
			},
			"errorCode": []
		}
	},
	edit : res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
		}
	},
	infos : res => {
		return {
			"status": 10000,
			"data": {
				 "prtid": "prtzvxejpyvzeyvdvmalnyrdb",
				 "uid": "cnlngqzpqybvodrlxajemkwagnxy",
				 "username": "cloud",
				 "name": "产品测试",
				 "type": 1,
				 "size": "CPE200",
				 "describe": "产品描述",
				 "status": 1,
				 "created_at": "2020-07-27 11:43:43"
			},
			"errorCode": []
		}
	},
	del : res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
		}
	},
	publish : res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
		}
	},
}

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
						   "username": "cloud",
						   "prtname": "产品测试",
						   "type": 1,
						   "size": "CPE200",
						   "status": 2,
						   "created_at": "2020-07-27 11:43:43"
					  }
				 ]
			},
			"errorCode": []
	   }
	},
	info:res => {
		return {
			"status": 10000,
			"data": {
				 "prtid": "prtzvxejpyvzeyvdvmalnyrdb",
				 "prtname": "产品测试",
				 "type": 1,
				 "size": "CPE200",
				 "status": 2,
				 "describe": "产品描述",
				 "uid": "cnlngqzpqybvodrlxajemkwagnxy",
				 "username": "cloud",
				 "created_at": "2020-07-27 11:43:43"
			},
			"errorCode": []
	   	}
	},
	audit:res => {
		return {
			"status": 10000,
			"data": {
				 "prtid": "prtzvxejpyvzeyvdvmalnyrdb",
				 "prtname": "产品测试",
				 "type": 1,
				 "size": "CPE200",
				 "status": 2,
				 "describe": "产品描述",
				 "uid": "cnlngqzpqybvodrlxajemkwagnxy",
				 "username": "cloud",
				 "created_at": "2020-07-27 11:43:43"
			},
			"errorCode": []
	   	}
	},
}

export default {
	all: res => {
		return {
			"status": 10000,
			"data": [
				{
					"gid": "cnlngqzpqybvodrlxajemkwagnxy",//工作组ID
					"user_id": "asadasdasdasdasdasdasd",//用户ID
					"name": "我的工作组",//工作组名称,根工作组名称分中英文，分割符为"#"
					"pid": 0,//父工作组ID,当为根工作组时，此值为0
					"child": [
						{
							"gid": "cnlapnvenbyqzwdlmxjmxgaoprkzasds",
							"user_id": 2,
							"name": "工作组1sadasdasdassad撒山东省风格多福多寿都是打死非非非都是",
							"pid": "cnlngqzpqybvodrlxajemkwagnxy",
							"child": [{
								"gid": "asdasdasdasdagfdfsdfsdfdas",
								"user_id": 22,
								"name": "工作组1-1",
								"pid": "cnlapnvenbyqzwdlmxjmxgaoprkzasds",
								"child": [{
									"gid": "fgfasdqwdasdgadasdasdasdsa",
									"user_id": 222,
									"name": "-1-1",
									"pid": "asdasdasdasdagfdfsdfsdfdas",
									"child": [{
										"gid": "gsdfsfsdfagasdasdad",
										"user_id": 222,
										"name": "sfdsd萨达所广告费的范德萨范德萨发生大打算sad大扫荡-1-1",
										"pid": "fgfasdqwdasdgadasdasdasdsa",
										"child": []
									}]
								}]
							}]
						},
						{
							"gid": "cnlpmxqgzaprmbvjqyjyoxkenwdo",
							"user_id": 2,
							"name": "工作组2",
							"pid": "cnlngqzpqybvodrlxajemkwagnxy",
							"child": []
						}
					]
				}
			],
			"errorCode": []
		}
	},
	info:res => {
		return {
			"status": 10000,
			"data": {
				 "user_id": "cnlngqzpqybvodrlxajemkwagnxy",
				 "pid": "cnlwmnaxqkpembgjvmlyonzvrwdz",
				 "code": "001",
				 "name": "工作组11",
				 "description": "创建工作组测试11",
				 "config_id": "08298587431592880309",
				 "auto": 0,
				 "level": 1,
				 "created_at": "2021-01-18 10:45:00",
				 "updated_at": "2021-01-18 10:45:00",
				 "gid": "cnlapnvenbyqzwdlmxjmxgaoprkz"
			},
			"errorCode": []
	   }
	},
	delete:res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
	   }
	},
	add:res => {
		return {
			"status": 10000,
			"data": {
				 "gid": "cnlpmxqgzaprmbvjqyjyoxkenwdo"
			},
			"errorCode": []
	   }
	},
	save:res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
	   }
	}
	
}

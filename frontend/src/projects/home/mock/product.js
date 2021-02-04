let tt = Math.random()*100;
export default {
	statics: res => {
		return {
			"status": 10000,
			"data": [
				 {
					  "name": "产品测试",
					  "devices_total": parseInt(Math.random()*100),
					  "devices_statics": {
						   "FAC7000": parseInt(Math.random()*100)>20?parseInt(Math.random()*100)-20:parseInt(Math.random()*100),
						   "FAC6000": parseInt(Math.random()*100)>20?20:20-parseInt(Math.random()*100)
					  }
				},
				{
					"name": "产品测试2",
					"devices_total": 20,
					"devices_statics": {
						 "FAC1000": 10,
						 "FAC2000": 10
					}
				},
				{
					"name": "产品测试3",
					"devices_total": 30,
					"devices_statics": {
						 "AP100": 15,
						 "AP300": 15
					}
			   	}
			],
			"errorCode": []
	   	}
	},
	
	
}

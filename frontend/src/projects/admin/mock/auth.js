export default {
	token: res => {
		return {
			"status": 10000,
			"data": {
				"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIiwianRpIjoieW93aWZpIn0.eyJpc3MiOiJodHRwOlwvXC93d3cueW93aWZpLm5ldCIsImF1ZCI6Imh0dHA6XC9cL3d3dy55b3dpZmkubmV0IiwianRpIjoieW93aWZpIiwiaWF0IjoxNTU3MDQ3MTg3LCJuYmYiOjE1NTcwNDcyNDcsImV4cCI6MTU1NzA1NDM4NywidWlkIjoiNDYifQ",
				"refresh_token": "NDZAZXlKMGVYQWlPaUpLVjFRaUxDSmhiR2NpT2lKdWIyNWxJaXdpYW5ScElqb2llVzkzYVdacEluMC5leUpwYzNNaU9pSm9kSFJ3T2x3dlhDOTNkM2N1ZVc5M2FXWnBMbTVsZENJc0ltRjFaQ0k2SW1oMGRIQTZYQzljTDNkM2R5NTViM2RwWm1rdWJtVjBJaXdpYW5ScElqb2llVzkzYVdacElpd2lhV0YwSWpveE5UVTNNRFEzTVRnM0xDSnVZbVlpT2pFMU5UY3dORGN5TkRjc0ltVjRjQ0k2TVRVMU56QTFORE00Tnl3aWRXbGtJam9pTkRZaWZRLkAxNTU3MDQ3MTg3",
				"expire": 7200,
				"refresh_token_expire": 30
			}
		}
	},
	refresh: res => {
		return {
			"status": 10000,
			"data": {
				 "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xOTIuMTY4LjMzLjEwOjgxNjNcL2F1dGhcL3Rva2VuXC9yZWZyZXNoIiwiaWF0IjoxNTkyMzg1NjE4LCJleHAiOjE1OTIzOTI4NDcsIm5iZiI6MTU5MjM4NTY0NywianRpIjoiVUoweWdtZTRpSXRCOEYySCIsInN1YiI6MiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.-r-O9_mj-c-8cRvTnbN9EoiYVgUAm_wb2wlWZ3gjiiE",
				 "token_type": "Bearer",
				 "expires_in": 7200,
				 "refresh_token": "ZXlKMGVYQWlPaUpLVjFRaUxDSmhiR2NpT2lKSVV6STFOaUo5LmV5SnBjM01pT2lKb2RIUndPbHd2WEM4eE9USXVNVFk0TGpNekxqRXdPamd4TmpOY0wyRjFkR2hjTDNSdmEyVnVJaXdpYVdGMElqb3hOVGt5TXpnME56QXpMQ0psZUhBaU9qRTFPVEl6T1RFNU1ETXNJbTVpWmlJNk1UVTVNak00TkRjd015d2lhblJwSWpvaWJWZHBlREF3UVdsWVlVSjRjWE5EU3lJc0luTjFZaUk2TWl3aWNISjJJam9pTWpOaVpEVmpPRGswT1dZMk1EQmhaR0l6T1dVM01ERmpOREF3T0RjeVpHSTNZVFU1TnpabU55SjkubEdJcEVQVDFzbTBJcFBRQ1FMUUt1MUxDbDV1c2tjXzZGaUt2ZC1UU01mOGNubGVuYnlxendkbG14cHF6ZGthbmpteGdhb3A=",
				 "refresh_expires_in": 43200
			},
			"errorCode": []
	   	}
	},
	destroy: res => {
		return {
			"status": 10000,
			"data": [],
			"errorCode": []
	   }
  	}    
}

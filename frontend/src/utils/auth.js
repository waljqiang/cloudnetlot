const TokenKey = 'system_token';

export function getToken() {
  	return window.sessionStorage.getItem(TokenKey);
}

export function setToken(token) {
 	 return window.sessionStorage.setItem(TokenKey,token);
}

export function removeToken() {
  	return window.sessionStorage.removeItem(TokenKey)
}


export function getSession(name) {
  	return window.sessionStorage.getItem(name);
}

export function setSession(name,val) {
  	return window.sessionStorage.setItem(name,val);
}

export function removeSession(name) {
  	return window.sessionStorage.removeItem(name)
}

export function saveToken(response){
	const data = response.data;
	setToken(data.access_token);
	setSession('refresh_token',data.refresh_token);
	setSession('expires',data.expires_in);

}

/*判断token是否过期*/
export function isTokenExpired() {
	let expiredTime = Number(getSession('expires'))/60;
	return expiredTime < 10;

}

export function getRefreshToken() { // 刷新token 注意这里用到的service
	let params = {
		refresh_token:getSession('refresh_token')
	}
	let baseUrl =  getSession('domain');
	return axios.post(baseUrl+"/"+requestRootName+'/backend/develop/auth/token/refresh',params)
	.then((res) => {
		return Promise.resolve(res.data)
	})
}
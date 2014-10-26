function FindProxyForURL(url,host){
	if(localHostOrDomainIs(host,"opsnode.raysns.com")) {
		return "PROXY 119.28.4.100:3129";
	} else {
		return "DIRECT";
	}
}
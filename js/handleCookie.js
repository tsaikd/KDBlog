function SetCookie(name, value, expires, path, domain, secure) {
	if (!navigator.cookieEnabled) {
//		alert("cookie disabled");
		return;
	}

	var expString = "";
	if (expires != null) {
		var expDays = expires*24*60*60*1000;
		var expDate = new Date();
		expDate.setTime(expDate.getTime()+expDays);
		expString = ";expires="+expDate.toGMTString();
	}

	document.cookie = (name + "=" + escape(value) + expString
		+ (path ? (";path="+path) : "")
		+ (domain ? (";domain="+domain) : "")
		+ (secure ? ";secure" : "")
	);
}

function GetCookie(name) {
	if (!navigator.cookieEnabled) {
//		alert("cookie disabled");
		return;
	}

	var result = "";
	var myCookie = document.cookie + ";";
	var searchName = name + "=";
	var startOfCookie = myCookie.indexOf(searchName);
	var endOfCookie;
	if (startOfCookie != -1) {
		startOfCookie += searchName.length;
		endOfCookie = myCookie.indexOf(";", startOfCookie);
		result = unescape(myCookie.substring(startOfCookie, endOfCookie));
	}
	return result;
}

function ClearCookie(name, path, domain) {
	if (!navigator.cookieEnabled) {
//		alert("cookie disabled");
		return;
	}

	if (GetCookie(name)) {
		document.cookie = (name + "="
			+ (path ? (";path="+path) : "")
			+ (domain ? (";domain="+domain) : "")
			+ ";expires=Thu, 01-Jan-1970 00:00:01 GMT"
		);
	}
}


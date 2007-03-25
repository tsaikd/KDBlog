function closeArticle(id) {
	var obj;

	if ((id == undefined) || (id == "displayArea")) {
		document.getElementById(id).innerHTML = "";
	} else {
		obj = document.getElementById(id);
		if (obj)
			obj.parentNode.removeChild(obj);
	}
}


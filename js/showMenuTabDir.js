function showMenuTabDir(obj, fpath, plist) {
	var showObj = obj.nextSibling;

	if (showObj.className != "menudir")
		return;
	if (showObj.attr_Loaded) {
		if (showObj.style.display == "none" || plist)
			showObj.style.display = "block";
		else
			showObj.style.display = "none";
		return;
	}

	var ajax = createAjax();
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				showObj.innerHTML = ajax.responseText;
				showObj.attr_Loaded = true;
				obj.attr_Loaded = true;

				if (plist && plist.length) {
					var buf = plist.shift();
					obj = getTagsNode(buf, showObj);
					if (obj)
						return showMenuTabDir(obj, fpath+"/"+buf, plist);
				}
			} else {
				alert('There was a problem with the request.');
			}
		}
	}
	ajax.open("POST", "data.php", true);
	ajax.setRequestHeader("Content-Type",
		"application/x-www-form-urlencoded; charset=utf-8");
	ajax.send("ftype=menutab_showDir&fpath="+fpath);
}


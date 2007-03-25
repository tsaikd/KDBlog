/*
flag can be ignore
	0x01: firstmonth
*/
function showMenuTabAll(id, ftype, fpath, flag) {
	if (flag == undefined)
		flag = 0;
	var showObj = document.getElementById(id);
	if (showObj.style.display == "none") {
		showObj.style.display = "block";
	} else {
		showObj.style.display = "none";
	}

	if (showObj.style.display != "none" && !showObj.attr_Loaded) {
	var ajax = createAjax();
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				showObj.innerHTML = ajax.responseText;
				showObj.attr_Loaded = true;

				if ((flag & 0x01) && showObj.childNodes[1]) {
					var node;
					node = showObj.childNodes[1];
					node.attr_Loaded = true;
				}
			} else {
				alert('There was a problem with the request.');
			}
		}
	}
	var cmd = "";
	if (flag & 0x01)
		cmd += "&firstmonth=true";
	ajax.open("POST", "data.php", true);
	ajax.setRequestHeader("Content-Type", 
		"application/x-www-form-urlencoded; charset=utf-8");
	ajax.send("id="+id+"&ftype="+ftype+"&fpath="+fpath+"&parentType="+id+"_"+cmd);
	}

}


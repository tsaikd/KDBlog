/*
childName can be ignore
*/
function chgMenuTag(tagId, childName) {
	var node;
	var showObj = document.getElementById("menutabContents");
	if (showObj.attr_CurMenuTag == undefined)
		showObj.attr_CurMenuTag = null;
	if (tagId == showObj.attr_CurMenuTag) {
		if (childName && tagId == "menutab_Tags")
			showMenuTabAll('menutags_'+childName, 'menutab_Tags_forceTag', 'tags/'+childName, 0x01);
		return;
	}

	var ajax = createAjax();
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 1) {
			document.getElementById(tagId).className += " selecting";
			showObj.innerHTML = "<div class='loading'>"+blog.lang.article.loading+"<\/div>";
		} else if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				if (showObj.attr_CurMenuTag)
					document.getElementById(showObj.attr_CurMenuTag).className = "menutab";
				document.getElementById(tagId).className = "menutab selected";
				showObj.attr_CurMenuTag = tagId;
				showObj.innerHTML = ajax.responseText;

				if (tagId == "menutab_Tags") {
					if (childName)
						showMenuTabAll('menutags_'+childName, 'menutab_Tags_forceTag', 'tags/'+childName, 0x01);
				} else if (tagId == "menutab_All" && showObj.childNodes[1]) {
					node = showObj.childNodes[1];
					node.attr_Loaded = true;
				}
			} else {
				alert('There was a problem with the request.');
			}
		}
	}
	ajax.open("POST", "data.php", true);
	ajax.setRequestHeader("Content-Type", 
		"application/x-www-form-urlencoded; charset=utf-8");
	ajax.send("ftype="+tagId);
}


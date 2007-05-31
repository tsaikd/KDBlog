/*
obj can be ignore
*/
function getTagsNode(tagName, obj) {
	if (!obj)
		obj = document.getElementById("menutabContents");

	var i;
	var node;
	var buf = getElementsByClass("menutext", obj);
	for (i=0 ; i<buf.length ; i++) {
		node = buf[i];
		if (node.innerHTML == tagName)
			return node;
	}

	return null;
}

function expandMenuTag(tagPath) {
	var i;
	var chkName;
	var nameList = tagPath.split("/");
	var node = document.getElementById("menutabContents");

	for (i=1 ; i<nameList.length ; i++) {
		node = getTagsNode(nameList[i], node);
		if (!node)
			return;
		if (node.attr_Loaded) {
			node.nextSibling.style.display = "block";
		} else {
			showMenuTabDir(node, nameList.slice(0, i+1).join("/"),
				nameList.slice(i+1, nameList.length));
			return;
		}
		node = node.nextSibling;
	}
}

/*
childName can be ignore
*/
function chgMenuTag(tagId, childName) {
	var buf;
	var node;
	var showObj = document.getElementById("menutabContents");
	if (showObj.attr_CurMenuTag == undefined)
		showObj.attr_CurMenuTag = null;
	if (tagId == showObj.attr_CurMenuTag) {
		if (childName) {
//			if (tagId == "menutab_Tags")
			expandMenuTag(childName);
		}
		return;
	}

	var ajax = createAjax();
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 1) {
			document.getElementById(tagId).className += " selecting";
			showObj.innerHTML = "<div class='loading'>"+lang.article.loading+"<\/div>";
		} else if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				if (showObj.attr_CurMenuTag)
					document.getElementById(showObj.attr_CurMenuTag).className = "menutab";
				document.getElementById(tagId).className = "menutab selected";
				showObj.attr_CurMenuTag = tagId;
				showObj.innerHTML = ajax.responseText;

				if (childName) {
//					if (tagId == "menutab_Tags")
					expandMenuTag(childName);
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


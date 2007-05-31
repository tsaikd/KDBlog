/*
position:
	0: "only" (default)
	1: "top"
	2: "bottom"

	0x10: noscroll
	0x20: force load
*/
function showArticle(fpath, position) {
	unSelectAllArticle();

	var buf;
	var node;
	var id = getIdFromPath(fpath);
	var showObj = document.getElementById(id);
	if (showObj == null) {
		showObj = document.getElementById("displayArea");
		node = document.createElement("div");
		node.setAttribute("class", "article");
		node.setAttribute("id", id);
		node.setAttribute("onmouseover", "selectArticle(this)");

		switch (position & 0x0F) {
		case 1:
			showObj.insertBefore(node, showObj.firstChild);
			break;
		case 2:
			showObj.appendChild(node);
			break;
		default:
			closeArticle();
			showObj.appendChild(node);
			break;
		}

		showObj = node;
	} else {
		position &= 0xF0;

		if (!(position & 0x20)) {
			scrollToArticle(showObj);
			selectArticle(showObj);
			return;
		}
	}

	var ajax = createAjax();
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 1) {
			buf = "<div name='toolbar' class='toolbar'>";
			buf += "<a name='close' onfocus='this.blur()' class='button' href='javascript:closeArticle(\""+id+"\")'>"+lang.article.toolbar.close+"<\/a>";
			buf += "<\/div>";
			buf += "<div class='loading'>"+lang.article.loading+"<\/div>";
			showObj.innerHTML = buf;
		} else if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				var i, j;
				var bufNode;
				var xmldoc = ajax.responseXML;

				buf = ajax.responseText;
				i = buf.indexOf("<div")
				i = buf.indexOf("<div", i+4)
				j = buf.lastIndexOf("<\/div>");
				buf = buf.substring(i, j);

				showObj.innerHTML = buf;

				if (!(position & 0x10)) {
					scrollToArticle(showObj);
					selectArticle(showObj);
				}
			} else {
				alert('There was a problem with the request.');
			}
		}
	}
	ajax.open("POST", "data.php", true);
	ajax.setRequestHeader("Content-Type", 
		"application/x-www-form-urlencoded; charset=utf-8");
	ajax.send("ftype=article&fpath="+fpath);
}


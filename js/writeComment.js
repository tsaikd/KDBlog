function reload_security(obj) {
	// because some browser will cache the src url
	if (obj == undefined)
		obj = document.getElementById("comment_img");
	if (!obj.attr_num)
		obj.attr_num = 1;
	else
		obj.attr_num++;
	obj.src = "security.php?num="+obj.attr_num;
}

function send_comment() {
	var obj = document.getElementById("comment_form");
	var comment = URLencode(obj.elements[0].value);
	var reg_num_check = obj.elements[1].value;
	var submit = obj.elements[2];
	var id = getNodeId(obj.parentNode);
	var path = getPathFromId(id);

	if (obj.disabled == undefined)
		obj.disabled = false;
	if (obj.disabled)
		return;

	obj.disabled = submit.disabled = true;

	var ajax = createAjax();
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				var node;
				var xmldoc = ajax.responseXML;

				if (xmldoc.getElementsByTagName('error').length) {
					node = xmldoc.getElementsByTagName('error')[0];
					var attr = node.attributes.getNamedItem("ename");
					if (attr.value == "reg_num_check")
						reload_security();
					alert(getNodeText(node));

					obj.disabled = submit.disabled = false;
					return;
				}

				closeArticle(id);
				showArticle(path, 0x01);
			}
		}
	}
	ajax.open("POST", "data.php", true);
	ajax.setRequestHeader("Content-Type", 
		"application/x-www-form-urlencoded; charset=utf-8");
	ajax.send("ftype=comment&comment="+comment+"&reg_num_check="+reg_num_check+"&fpath="+path);
}

// in Opera 9, modify article contents with innerHTML will destroy javascript
// So, it's necessary to create a child div to modify
function commentArticle(id) {
	var obj = document.getElementById(id);

	var node;
	node = document.getElementById("comment_form");
	if (node && (node.parentNode != obj)) {
		alert(blog.lang.comment.errmsg.multiComment);
		return;
	}

	node = findChildByName(obj, "comment");
	var btnObj = findChildByName(obj, "toolbar");
	btnObj = findChildByName(btnObj, "comment");
	if (node) {
		btnObj.className = "button";
		node.parentNode.removeChild(node);
	} else {
		btnObj.className = "button selected";
		node = document.createElement("form");
		node.setAttribute("name", "comment");
		node.setAttribute("id", "comment_form");
		node.setAttribute("class", "writeComment");
		node.setAttribute("action", "javascript:send_comment()");
		obj.appendChild(node);

		var showText = "";

		showText += "<textarea name='comment' rows='8' cols='60'></textarea><br />";
		showText += "<img id='comment_img' src='security.php' onclick='javascript:reload_security()' />";
		showText += "<input name='reg_num_check' type='text' size='4' maxlength='4' />";
		showText += "<input type='submit' value='"+blog.lang.button.submit+"' />";
		node.innerHTML = showText;
	}
}


function reload_security(obj) {
	// because some browser will cache the src url
	if (obj == undefined)
		obj = document.getElementById("comment_img");
	conf.func.comment.img_num++;
	obj.src = "security.php?num="+conf.func.comment.img_num;

	obj = findChildByName(obj.parentNode, "reg_num_check");
	if (obj) {
		obj.value = "";
		obj.focus();
	}
}

function send_comment() {
	var obj = document.getElementById("comment_form");

	var user = obj.elements[0].value;
	SetCookie("user", user);
	user = "&user="+URLencode(user);
	var email = obj.elements[1].value;
	SetCookie("email", email);
	email = "&email="+URLencode(email);
	var notify = obj.elements[2].checked ? "&notify=y" : "";
	var comment = "&comment="+URLencode(obj.elements[3].value);
	var reg_num_check = "&reg_num_check="+URLencode(obj.elements[4].value);
	var submit = obj.elements[5];

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
	ajax.send("ftype=comment"+user+email+notify+comment+reg_num_check+"&fpath="+path);
}

// in Opera 9, modify article contents with innerHTML will destroy javascript
// So, it's necessary to create a child div to modify
function commentArticle(id) {
	var obj = document.getElementById(id);

	var node;
	node = document.getElementById("comment_form");
	if (node && (node.parentNode != obj)) {
		alert(lang.comment.errmsg.multiComment);
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

		showText += "<table><tr>";
		showText += "<td>ID:<\/td>";
		showText += "<td><input name='user' type='text' size='20' value='"+GetCookie("user")+"' /><\/td>";
		showText += "<\/tr><tr>";
		showText += "<td>E-Mail:<\/td>";
		showText += "<td><input name='email' type='text' size='50' value='"+GetCookie("email")+"' /><\/td>";
		showText += "<\/tr><\/table>";
		if (conf.func.comment.notify)
			showText += "<input type='checkbox' name='notify' value='y' />"+lang.comment.write.notify+"<br />";
		else
			showText += "<input type='checkbox' name='notify' value='y' style='display: none' />";
		showText += "<div><a href='javascript:;' onclick='javascript: toggleObj(this.nextSibling, \"block\");'>"+lang.comment.write.validTags+": <\/a><div style='display: none;'>&lt;a href=\"\" title=\"\"&gt;</div><\/div>";
		showText += "<textarea name='comment' rows='8' cols='60'><\/textarea><br />";
		showText += "<table><tr>";
		showText += "<td><img id='comment_img' src='security.php' onclick='javascript:reload_security()' /><\/td>";
		showText += "<td><input name='reg_num_check' type='text' size='4' maxlength='4' /><\/td>";
		showText += "<td><input type='submit' value='"+lang.button.submit+"' /><\/td>";
		showText += "<\/tr><\/table>";
		node.innerHTML = showText;

		scrollToArticle(node);
		reload_security();
	}
}


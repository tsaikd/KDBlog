function runSpecFile(fpath) {
	var ajax = createAjax();
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				var node;
				var xmldoc = ajax.responseXML;

				node = xmldoc.getElementsByTagName('error')[0];
				if (node) {
					alert(blog.lang.special.runSpecError+": "+fpath+"\n"+getNodeText(node));
				} else {
					alert(blog.lang.special.runSpecOk);
				}
			} else {
				alert('There was a problem with the request.');
			}
		}
	}
	ajax.open("POST", "data.php", true);
	ajax.setRequestHeader("Content-Type",
		"application/x-www-form-urlencoded; charset=utf-8");
	ajax.send("ftype=runspec&fpath="+fpath);
}


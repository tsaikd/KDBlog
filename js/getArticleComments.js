function getArticleComments(xmldoc) {
	var i;
	var buf;
	var node;
	var attr;
	var cNode;
	var cHeaderNode;
	var cFromNode;
	var cTimeNode;
	var cDataNode;

    var obj = document.createElement("div");
    obj.setAttribute("name", "comments");
    obj.setAttribute("class", "comments");

	node = xmldoc.getElementsByTagName('comment');
	if (!node.length)
		return null;

	for (i=0 ; i<node.length ; i++) {
		cNode = document.createElement("div");
		cNode.setAttribute("class", "comment");

		cHeaderNode = document.createElement("div");
		cHeaderNode.setAttribute("class", "commentHeader");
		cNode.appendChild(cHeaderNode);

		attr = node[i].attributes.getNamedItem("ip");
		if (attr) {
			cFromNode = document.createElement("span");
			cFromNode.setAttribute("class", "commentFrom");
			cFromNode.innerHTML = "From: "+attr.value;
			cHeaderNode.appendChild(cFromNode);
		}

		attr = node[i].attributes.getNamedItem("time");
		if (attr) {
			buf = new Date();
			buf.setTime(attr.value*1000);
			cTimeNode = document.createElement("span");
			cTimeNode.setAttribute("class", "commentTime");
			cTimeNode.innerHTML = buf.getYear()+"/"+buf.getMonth()+"/"+buf.getDate()+" "+buf.getHours()+":"+buf.getMinutes()+":"+buf.getSeconds();
			cHeaderNode.appendChild(cTimeNode);
		}

		buf = getNodeText(node[i]);
		cDataNode = document.createElement("div");
		cDataNode.setAttribute("class", "commentData");
		cDataNode.innerHTML = "<pre>"+buf+"</pre>";
		cNode.appendChild(cDataNode);

		obj.appendChild(cNode);
	}

	return obj;
}


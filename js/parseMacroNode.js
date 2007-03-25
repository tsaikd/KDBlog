function parseMacroNode(xmldoc, node) {
	var attr;
	var buf;
	var pNode = node.parentNode;

	switch (node.nodeName) {
		case "quote":
			buf = xmldoc.createElement("div");

			attr = xmldoc.createAttribute("class");
			attr.value = "macro_"+node.nodeName;
			buf.setAttributeNode(attr);

			attr = getNodeText(node);
			attr = attr.replace(/</g, "&lt;");
			attr = attr.replace(/>/g, "&gt;");

//			attr = xmldoc.createTextNode(attr);
			attr = xmldoc.createCDATASection(attr);
			buf.appendChild(attr);

			pNode.replaceChild(buf, node);
			break;
		default:
			alert("Macro: '"+node.nodeName+"' is not implement");
			break;
	}
}


/*
flag:
0x01: hide root tag
 */
function getNodeXml(node, flag) {
	var i;
	var buf;
	var bufNode;
	var res = "";

	if (!flag)
		flag = 0;

	if (!(flag & 0x01)) {
		res += "<"+node.nodeName;
		for (i=0 ; i<node.attributes.length ; i++) {
			bufNode = node.attributes[i];
			res += " "+bufNode.name+"='"+bufNode.value+"'";
		}
		res += ">";
	}

	for (i=0 ; i<node.childNodes.length ; i++) {
		bufNode = node.childNodes[i];
		switch (bufNode.nodeType) {
		case 1: // Element node
			res += getNodeXml(bufNode);
			break;
		case 3: // Text node
			buf = getNodeText(bufNode, 0x03);
			if (buf.length <= 1)
				continue;
			res += buf;
			break;
		default:
			res += getNodeText(bufNode, 0x03);
			break;
		}
	}

	if (!(flag & 0x01))
		res += "</"+node.nodeName+">";

	return res;
}


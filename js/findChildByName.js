function findChildByName(obj, name) {
	var i;
	var attr;
	var node;
	var res;

	for (i=0 ; i<obj.childNodes.length ; i++) {
		node = obj.childNodes[i];

		if (node.nodeType != 1)
			continue;

		attr = node.attributes.getNamedItem("name");
		if (attr && (attr.value == name))
			return node;
	}
	return null;
}


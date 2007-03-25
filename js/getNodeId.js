function getNodeId(obj) {
	var node = obj.attributes.getNamedItem("id");
	if (node)
		return node.value;
	else
		return null;
}


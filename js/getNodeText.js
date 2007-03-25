/*
flag:
	0x01: trim first space
	0x02: trim last space
*/
function getNodeText(node, flag) {
	var res;

	if (node.textContent) {
		res = node.textContent;
	} else if (node.xml) { // for IE
		if (node.nodeType == 3)
			res = node.xml;
		else
			res = node.text;
	} else { // html DOM
		res = node.innerHTML;
	}

	if (res == undefined)
		return "";

	if (!flag)
		flag = 0;

	var iStart = 0;
	var iLength = res.length;

	if (flag & 0x03) {
		if (flag & 0x01) {
			if (res.charAt(0) == "\r") { // for IE
				iStart+=2;
				iLength-=2;
			} else if (res.charAt(0) == "\n") {
				iStart++;
				iLength--;
			}
		}

		if (flag & 0x02) {
			if (res.charAt(res.length-1) == "\n")
				iLength--;
			if (res.charAt(res.length-2) == "\r") // for IE
				iLength--;
		}

		res = res.substr(iStart, iLength);
	}

	return res;
}


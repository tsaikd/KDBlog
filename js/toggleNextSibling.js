function toggleNextSibling(obj) {
	var showObj = obj.nextSibling;
	if (!showObj)
		return;

	if (showObj.style.display == "none")
		showObj.style.display = "block";
	else
		showObj.style.display = "none";
}


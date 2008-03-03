function toggleObj(obj, toggle, display) {
	if (display == undefined)
		display = "";
	if (obj.style.display != toggle) {
		obj.style.display = toggle;
	} else {
		obj.style.display = display;
	}
}


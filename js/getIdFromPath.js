function getIdFromPath(path) {
	var id = path;
	id = id.replace(/\//g, "__");
	id = id.replace(/\./g, "____");
	return id;
}

function getPathFromId(id) {
	var path = id;
	path = path.replace(/____/g, ".");
	path = path.replace(/__/g, "/");
	return path;
}


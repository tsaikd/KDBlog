function transPath2Date(path) {
	var buf = path.split("/");
	var len = buf.length;
    if (len < 3)
        return "";
    return buf[len-3]+"/"+buf[len-2]+"/"+buf[len-1].substr(0, 2);
}


function URLencode(sStr) {
	return escape(sStr)
		.replace(/\+/g, '%2B')
		.replace(/\"/g, '%22')
		.replace(/\'/g, '%27');
}


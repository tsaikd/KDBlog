<?php
function isValidPath($fpath) {
	$basepath = realpath(".")."/";
	$fpath = $basepath.$fpath;

	while (true) {
		$fbuf = ereg_replace("/\./", "/", $fpath);
		if ($fbuf == $fpath)
			break;
		$fpath = $fbuf;
	}
	while (true) {
		$fbuf = ereg_replace("/([^/]*)/\.\./", "/", $fpath);
		if ($fbuf == $fpath)
			break;
		$fpath = $fbuf;
	}

	if (strlen($fpath) <= strlen($basepath))
		return false;
	if (strncmp($fpath, $basepath, strlen($basepath)) != 0)
		return false;
	return true;
}

function isValidArticlePath($vpath) {
	if (substr($vpath, 0, 5) != "data/")
		return false;
	if (substr($vpath, -4) != ".xml")
		return false;
	if (ereg("/\.", $vpath))
		return false;

	return true;
}

?>

<?php
function smartSymLink($fpath, $tpath) {
	if (file_exists($tpath))
		return false;

	$dpath = dirname($tpath);
	if (!file_exists($dpath)) {
		include_once("php/mkdir_ex.php");
		if (!mkdir_ex($dpath))
			return false;
	}

	$fpath = realpath($fpath);
	return symlink($fpath, $tpath);
}
?>

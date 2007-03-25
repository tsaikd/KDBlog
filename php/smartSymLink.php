<?php
function smartSymLink($fpath, $tpath) {
	$tbuf = explode("/", $tpath);

	if ($tbuf[0] == "")
		$dpath = "/";
	else
		$dpath = $tbuf[0]."/";

	$iCount = count($tbuf) - 1;
	for ($i=1 ; $i<$iCount ; $i++) {
		$dpath = $dpath.$tbuf[$i]."/";
		if (!file_exists($dpath)) {
			if (!mkdir($dpath))
				return false;
		}
	}

	$fpath = realpath($fpath);
	return symlink($fpath, $tpath);
}
?>

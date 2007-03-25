<?php
include_once("php/rm_ex.php");

function cleanDir($path) {
	if (!is_dir($path) || !is_writable($path))
		return false;

	$res = true;

	$d = dir($path);
	while ($res && ($f = $d->read())) {
		if ($f[0] == ".")
			continue;
		$res = $res && rm_ex("$path/$f");
	}
	$d->close();

	return $res;
}
?>

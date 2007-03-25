<?php
function getDir($path) {
	$aRes = array();

	if (!is_dir($path))
		return $aRes;

	$d = dir($path);
	while ($f = $d->read()) {
		if ($f[0] == ".")
			continue;
		array_push($aRes, $f);
	}
	$d->close();
	sort($aRes);

	return $aRes;
}
?>

<?php
function mkdir_ex($path, $mode=null) {
	$res = true;
	$pdir = dirname($path);
	if (!is_dir($pdir))
		$res = $res && mkdir_ex($pdir, $mode);

	if ($mode != null)
		$res = $res && mkdir($path, $mode);
	else
		$res = $res && mkdir($path);

	return $res;
}
?>

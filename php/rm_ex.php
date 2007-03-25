<?php
function rm_ex($path) {
	if (is_link($path))
		return unlink($path);
	if (!file_exists($path))
		return true;
	if (is_dir($path)) {
		$res = true;
		$d = dir($path);
		while ($res && ($f = $d->read())) {
			if ($f == ".")
				continue;
			if ($f == "..")
				continue;
			$res = $res && rm_ex("$path/$f");
		}
		$d->close();
		$res = $res && rmdir($path);
		return $res;
	} else {
		return unlink($path);
	}
}
?>

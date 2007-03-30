<?php
function getRecentCommentPath($numLimit) {
	include_once("php/getDir.php");
	global $BLOGCONF;
	$res = array();

	if (!$numLimit)
		return $res;

	$cpath = realpath($BLOGCONF["cmntpath"]);
	$cplen = strlen($cpath);
	$dpath = $BLOGCONF["func"]["comment"]["indexByTime"];
	$darray = getDir($dpath);
	foreach($darray as $f) {
		$fpath = realpath($dpath."/".$f);
		if (!$fpath)
			continue;

		$fpath = substr($fpath, $cplen+1);
		if (!$fpath)
			continue;

		array_push($res, $fpath);
		if (count($res) >= $numLimit)
			return $res;
	}

	return $res;
}
?>

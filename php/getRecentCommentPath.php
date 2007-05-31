<?php
function getRecentCommentPath($numLimit) {
	include_once("php/getDir.php");
	global $CONF;
	$res = array();

	if (!$numLimit)
		return $res;

	$cpath = realpath($CONF["path"]["comment"]);
	$cplen = strlen($cpath);
	$dpath = $CONF["func"]["comment"]["indexByTime"];
	$darray = getDir($dpath);
	rsort($darray);
	foreach($darray as $f) {
		$fpath = realpath($dpath."/".$f);
		if (!$fpath)
			continue;
		if (substr($fpath, -4) != ".xml")
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

<?php
include_once("php/transPath.php");

/*
flag:
	0x01: include a extra nonexists path
	0x02: get target comment path from $info["cname"]
*/
function getArticleCommentPath($vArticlePath, $flag=0, $info=null) {
	$res = array();

	$vCommentPath = "comment/".substr($vArticlePath, 5);
	$fCommentPath = transPathV2R($vCommentPath);

	if ($flag & 0x02) {
		array_push($res, dirname($fCommentPath)."/".$info["cname"]);
		return $res;
	}

	$buf = substr($fCommentPath, 0, -4);
	$i = 1;
	$fCommentPath = sprintf("%s_%d.xml", $buf, $i);
	while (file_exists($fCommentPath)) {
		array_push($res, $fCommentPath);
		$i++;
		$fCommentPath = sprintf("%s_%d.xml", $buf, $i);
	}

	if ($flag & 0x01)
		array_push($res, $fCommentPath);

	return $res;
}

?>

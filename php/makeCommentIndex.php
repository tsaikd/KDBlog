<?php
include_once("php/parseXml.php");
include_once("php/smartSymLink.php");

/**
 * $tcom == NULL : detect time of comment automatically
 **/
function makeCommentIndex($fcom, $tcom = NULL) {
	global $CONF;

	$didx = $CONF["func"]["comment"]["indexByTime"];

	if ($tcom === NULL) {
		list($index, $vals) = parseXml($fcom);
		$tcom = $vals[$index["comment"][0]]["attributes"]["time"];
	}

	$fidx = $didx."/".$tcom;
	smartSymLink($fcom, $fidx);
}
?>

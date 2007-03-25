<?php
include_once("php/getArticleCommentPath.php");

function writeArticleComment($vArticlePath, $comment) {
	$comment = str_replace("\r\n", "\n", $comment);

	$aPath = getArticleCommentPath($vArticlePath, 0x01);
	$fCommentPath = array_pop($aPath);

	// check dir
	$buf = dirname($fCommentPath);
	if (!file_exists($buf)) {
		include_once("php/mkdir_ex.php");
		mkdir_ex($buf);
	}

	$fp = fopen($fCommentPath, "w");
	fwrite($fp, '<?xml version="1.0" encoding="utf-8" ?>'."\n");
	fwrite($fp, '<comment ip="'.$_SERVER["REMOTE_ADDR"].'" time="'.$_SERVER["REQUEST_TIME"].'"><![CDATA['."\n");
	fwrite($fp, $comment."\n");
	fwrite($fp, ']]></comment>'."\n");
	fclose($fp);
}

?>

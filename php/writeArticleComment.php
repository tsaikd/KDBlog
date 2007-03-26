<?php
include_once("php/getArticleCommentPath.php");

function writeArticleComment($vArticlePath, $comment, $user=null) {
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
	fwrite($fp, '<comment');
	fwrite($fp, ' ip="'.$_SERVER["REMOTE_ADDR"].'"');
	fwrite($fp, ' time="'.$_SERVER["REQUEST_TIME"].'"');
	if ($user != null)
	fwrite($fp, ' user="'.$user.'"');
	fwrite($fp, '><![CDATA['."\n");
	fwrite($fp, $comment."\n");
	fwrite($fp, ']]></comment>'."\n");
	fclose($fp);
}

?>

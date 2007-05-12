<?php
include_once("php/getArticleCommentPath.php");
include_once("php/parseXml.php");

/*
$info := (array) or null
	$info["user"] := (string) user name
	$info["email"] := (string) user email
	$info["notify"] := (bool) notify user if new comment
*/
function writeArticleComment($vArticlePath, $comment, $info=null) {
	global $BLOGCONF;

	$comment = str_replace("\r\n", "\n", $comment);

	$aPath = getArticleCommentPath($vArticlePath, 0x01);
	$fCommentPath = array_pop($aPath);

	// check dir
	$buf = dirname($fCommentPath);
	if (!file_exists($buf)) {
		include_once("php/mkdir_ex.php");
		mkdir_ex($buf);
	}

	// write comment
	$fp = fopen($fCommentPath, "w");
	fwrite($fp, '<?xml version="1.0" encoding="utf-8" ?>'."\n");
	fwrite($fp, '<comment');
	fwrite($fp, ' ip="'.$_SERVER["REMOTE_ADDR"].'"');
	fwrite($fp, ' time="'.$_SERVER["REQUEST_TIME"].'"');

	if ($info["user"])
		fwrite($fp, ' user="'.$info["user"].'"');
	if ($info["email"])
		fwrite($fp, ' email="'.$info["email"].'"');
	if ($info["notify"])
		fwrite($fp, ' notify="notify"');

	fwrite($fp, '><![CDATA['."\n");
	fwrite($fp, $comment."\n");
	fwrite($fp, ']]></comment>'."\n");
	fclose($fp);

	// Set index
	include_once("php/smartSymLink.php");
	$indexPath = $BLOGCONF["func"]["comment"]["indexByTime"]."/".$_SERVER["REQUEST_TIME"];
	smartSymLink($fCommentPath, $indexPath);

	// Check index number
	include_once("php/rm_ex.php");
	include_once("php/getDir.php");
	$dpath = $BLOGCONF["func"]["comment"]["indexByTime"];
	$buf = getDir($dpath);
	$iCount = count($buf) - $BLOGCONF["func"]["comment"]["indexNum"];
	for ($i=0 ; $i<$iCount ; $i++)
		rm_ex($dpath."/".$buf[$i]);

	// Check notify email list
	include_once("php/getArticleTitle.php");
	global $BLOGLANG;
	$title = getArticleTitle($vArticlePath);
	$subject = sprintf($BLOGLANG["comment"]["email"]["fSubject"], $title);
	$header = "Content-type: text/html; charset=utf-8\n";
	$rmurl  = $BLOGCONF["link"]."data.php?ftype=commentUnNotify";
	$rmurl .= "&fpath=".$vArticlePath;
	$body = "<a href='".$BLOGCONF["link"]."index.php?fpath=".$vArticlePath."'>".$title."</a><br />";
	if ($info["user"])
		$body .= $info["user"].":<br />";
	$body .= "<pre style='background-color: #eef; margin-left: 2em;'>".$comment."</pre>";

	if ($BLOGCONF["func"]["commentTrack"]["enable"])
		mail($BLOGCONF["email"], $subject, $body, $header);

	if (!$BLOGCONF["func"]["commentNotify"]["enable"])
		return;
	foreach($aPath as $f) {
		$xml = parseXml($f);
		$index = $xml["index"];
		$vals = $xml["vals"];

		$i = $index["comment"][0];
		if ($i === null)
			continue;
		if ($vals[$i]["attributes"]["notify"] != "notify")
			continue;
		$email = $vals[$i]["attributes"]["email"];
		if (!$email)
			continue;

		$buf  = $rmurl;
		$buf .= "&cname=".basename($f);
		$buf .= "&ip=".$vals[$i]["attributes"]["ip"];
		$buf .= "&time=".$vals[$i]["attributes"]["time"];
		$ebody = $body.sprintf($BLOGLANG["comment"]["email"]["fBodyTail"], $buf);

		mail($email, $subject, $ebody, $header);
	}
}

?>

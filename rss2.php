<?php

include_once("config.php");
include_once("php/showArticle.php");

if ($BLOGCONF["func"]["debug"]["enable"])
	header("Content-Type: text/xml");
else
	header("Content-Type: application/rss+xml");
header("Pragma: no-cache");
header("Expires: 0");
echo '<?xml version="1.0" encoding="utf-8" ?>';
echo '<rss version="2.0">';
echo '<channel>';
echo '<title>'.$BLOGCONF["title"].'</title>';
echo '<description>'.$BLOGCONF["description"].'</description>';
echo '<link>'.$BLOGCONF["link"].'</link>';
$lang = $BLOGCONF["language"];
$lang = strtolower($lang);
$lang = str_replace("_", "-", $lang);
echo "<language>$lang</language>";
echo '<managingEditor>'.$BLOGCONF["email"].'</managingEditor>';
echo '<docs>'.$BLOGCONF["link"].'rss2.php?feed='.$_REQUEST["feed"].'</docs>';

if ($_REQUEST["limit"]) {
	$limit = (int)$_REQUEST["limit"];
	if ($limit > $BLOGCONF["rssMaxNum"])
		$limit = $BLOGCONF["rssMaxNum"];
} else {
	$limit = $BLOGCONF["rssDefNum"];
}

switch($_REQUEST["feed"]) {
default: // all
	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath($BLOGCONF["datapath"], $limit);
	foreach ($farray as $fpath) {
		$vpath = transPathR2V($fpath, "data");
		getCacheArticle($vpath, "rss");
	}
	break;
}

echo '</channel>';
echo '</rss>';

?>

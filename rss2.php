<?php

include_once("config.php");
include_once("php/showArticle.php");

if ($CONF["func"]["debug"]["enable"])
	header("Content-Type: text/xml");
else
	header("Content-Type: application/rss+xml");
header("Pragma: no-cache");
header("Expires: 0");

echo '<?xml version="1.0" encoding="utf-8" ?>';
?>
<rss version="2.0">
<channel>
<title><?=$CONF["title"]?></title>
<description><?=$CONF["description"]?></description>
<link><?=$CONF["link"]?></link>
<language><?=$CONF["langtype"]["rss2"]?></language>
<managingEditor><?=$CONF["email"]?></managingEditor>
<docs><?=$CONF["link"]?>rss2.php?feed=<?=$_REQUEST["feed"]?></docs>

<?php
if ($_REQUEST["limit"]) {
	$limit = (int)$_REQUEST["limit"];
	if ($limit > $CONF["rssMaxNum"])
		$limit = $CONF["rssMaxNum"];
} else {
	$limit = $CONF["rssDefNum"];
}

switch($_REQUEST["feed"]) {
default: // all
	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath($limit);
	foreach ($farray as $vpath) {
		getCacheArticle($vpath, "rss");
	}
	break;
}

?>
</channel>
</rss>


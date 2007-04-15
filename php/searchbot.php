<?php
function searchbot() {
	global $BLOGCONF;
	global $BLOGLANG;

	if (!$BLOGCONF["func"]["searchbot"]["enable"]) {
		echo "<html>";
		echo "<error type='$ftype' ename='funcOff'>";
		echo $BLOGLANG["message"]["funcOff"];
		echo "</error>";
		echo "</html>";
		return;
	}

	logecho("<html>");

	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath(-1);
	foreach ($farray as $vpath) {
		logecho("<a href='".$BLOGCONF["link"]."index.php?fpath=".$vpath."'>");
		logecho("$vpath</a><br />\n");
	}
	logecho("</html>");
}

function sitemap() {
	global $BLOGCONF;

	echo '<?xml version="1.0" encoding="utf-8" ?>';
	echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">';

	echo '<url>';
	echo '<loc>'.$BLOGCONF["link"].'</loc>';
	$ftime = filectime("index.php");
	echo '<lastmod>'.strftime("%Y-%m-%d", $ftime).'</lastmod>';
	echo '</url>';

	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath(-1);
	foreach ($farray as $vpath) {
		$fpath = transPathV2R($vpath);
		echo '<url>';
		echo '<loc>'.$BLOGCONF["link"]."index.php?fpath=".$vpath.'</loc>';
		$ftime = filectime($fpath);
		echo '<lastmod>'.strftime("%Y-%m-%d", $ftime).'</lastmod>';
		echo '</url>';
	}

	echo '</urlset>';
}
?>

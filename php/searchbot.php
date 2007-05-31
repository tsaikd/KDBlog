<?php
function searchbot() {
	global $CONF;
	global $LANG;

	if (!$CONF["func"]["searchbot"]["enable"]) {
		echo "<html>";
		echo "<error type='$ftype' ename='funcOff'>";
		echo $LANG["message"]["funcOff"];
		echo "</error>";
		echo "</html>";
		return;
	}

	logecho("<html>");

	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath(-1);
	foreach ($farray as $vpath) {
		logecho("<a href='".$CONF["link"]."index.php?fpath=".$vpath."'>");
		logecho("$vpath</a><br />\n");
	}
	logecho("</html>");
}

function sitemap() {
	global $CONF;

	echo '<?xml version="1.0" encoding="utf-8" ?>';
	echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">';

	echo '<url>';
	echo '<loc>'.$CONF["link"].'</loc>';
	$ftime = filectime("index.php");
	echo '<lastmod>'.strftime("%Y-%m-%d", $ftime).'</lastmod>';
	echo '</url>';

	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath(-1);
	foreach ($farray as $vpath) {
		$fpath = transPathV2R($vpath);
		echo '<url>';
		echo '<loc>'.$CONF["link"]."index.php?fpath=".$vpath.'</loc>';
		$ftime = filectime($fpath);
		echo '<lastmod>'.strftime("%Y-%m-%d", $ftime).'</lastmod>';
		echo '</url>';
	}

	echo '</urlset>';
}
?>

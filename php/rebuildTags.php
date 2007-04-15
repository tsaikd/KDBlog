<?php
include_once("php/getRecentArticlePath.php");
include_once("php/transPath.php");
include_once("php/parseXml.php");
include_once("php/smartSymLink.php");

function rebuildTags() {
	$farray = getRecentArticlePath(-1);

	foreach ($farray as $vpath) {
		$fpath = transPathV2R($vpath);
		$xml = parseXml($fpath);
		$index = $xml["index"];
		$vals = $xml["vals"];

		if (!$index["tag"])
			continue;

		foreach ($index["tag"] as $iTag) {
			$tag = $vals[$iTag]["value"];
			$vtag = "tags/$tag";
			$tagvpath = $vtag.substr($vpath, 4);
			$tagfpath = transPathV2R($tagvpath);
			smartSymLink($fpath, $tagfpath);
		}
	}
}
?>

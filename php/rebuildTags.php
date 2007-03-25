<?php
include_once("php/getRecentArticlePath.php");
include_once("php/smartSymLink.php");

function rebuildTags($frompath, $topath, $level=3) {
	$darray = getRecentArticlePath($frompath, -1);

	foreach ($darray as $fpath) {
		$xml = xml_parser_create("UTF-8");
		xml_parse_into_struct($xml, file_get_contents($fpath), $vals, $index);
		xml_parser_free($xml);

		if (!$index["TAG"])
			continue;

		foreach ($index["TAG"] as $iTag) {
			$tag = $vals[$iTag]["value"];

			if (!file_exists("$topath/$tag"))
				mkdir("$topath/$tag");

			$iSkip = strlen($frompath);
			if (substr($frompath, -1) != "/")
				$iSkip++;
			$tagpath = "$topath/$tag/".substr($fpath, $iSkip);
			if (!file_exists($tagpath))
				smartSymLink($fpath, $tagpath);
		}
	}
}
?>

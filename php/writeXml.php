<?php
function writeXml($fpath, $vals) {
	$buf = '<?xml version="1.0" encoding="utf-8" ?>'."\n";

	foreach($vals as $b) {
		switch($b["type"]) {
		case "open":
			$buf .= "<".$b["tag"];
			if (isset($b["attributes"])) {
				foreach($b["attributes"] as $k => $v)
					$buf .= sprintf(" %s=\"%s\"", $k, $v);
			}
			$buf .= "><![CDATA[".$b["value"]."]]>";
			break;
		case "close":
			$buf .= "</".$b["tag"].">";
			break;
		case "complete":
			$buf .= "<".$b["tag"];
			if (isset($b["attributes"])) {
				foreach($b["attributes"] as $k => $v)
					$buf .= sprintf(" %s=\"%s\"", $k, $v);
			}
			$buf .= "><![CDATA[".$b["value"]."]]>";

			$buf .= "</".$b["tag"].">";
			break;
		default: // "cdata"
			$buf .= "<![CDATA[".$b["value"]."]]>";
			break;
		}
	}
	$buf .= "\n";

	$fp = fopen($fpath, "w");
	fwrite($fp, $buf);
	fclose($fp);
}
?>

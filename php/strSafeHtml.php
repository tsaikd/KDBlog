<?php
function strSafeHtml($text) {
/*
	$text = str_replace("\\\\", "\\", $text);
	$text = str_replace("\\\"", "\"", $text);
	$text = str_replace("\\'", "'", $text);
//*/
	$text = stripslashes($text);
	$text = htmlentities($text);
	$text = preg_replace_callback("/%u[0-9A-Za-z]{4}/", toUtf8, $text);
	return $text;
}

function strCommentHtml($text) {
/*
	$text = str_replace("\\\\", "\\", $text);
	$text = str_replace("\\\"", "\"", $text);
	$text = str_replace("\\'", "'", $text);
//*/
	$text = stripslashes($text);
	$text = preg_replace_callback("/<(.*?)>/", filterCommentTags, $text);
	$text = preg_replace_callback("/%u[0-9A-Za-z]{4}/", toUtf8, $text);
	return $text;
}

function filterCommentTags($ar) {
	if (preg_match("/<(\w+)(.*?)>/", $ar[0], $match)) {
		$match[1] = strtolower($match[1]);
		switch ($match[1]) {
		case "a":
			$res = "<".$match[1];
			if (preg_match("/\shref=['\"](.*?)['\"]/i", $match[2], $attr))
				$res .= " href=\"".$attr[1]."\"";
			if (preg_match("/\stitle=['\"](.*?)['\"]/i", $match[2], $attr))
				$res .= " title=\"".$attr[1]."\"";
			$res .= ">";
			break;
		default:
			$res = htmlentities($match[0]);
			break;
		}
	} else if (preg_match("|</(\w+)>|", $ar[0], $match)) {
		$match[1] = strtolower($match[1]);
		switch ($match[1]) {
		case "a":
			$res = "</".$match[1].">";
			break;
		default:
			$res = htmlentities($match[0]);
			break;
		}
	}

	return $res;
}

function toUtf8($ar) {
	foreach ($ar as $val) {
		$val = intval(substr($val, 2), 16);
		if ($val < 0x7F) {			// 0000-007F
			$c .= chr($val);
		} else if ($val < 0x800) {	// 0080-0800
			$c .= chr(0xC0 | ($val / 64));
			$c .= chr(0x80 | ($val % 64));
		} else {					// 0800-FFFF
			$c .= chr(0xE0 | (($val / 64) / 64));
			$c .= chr(0x80 | (($val / 64) % 64));
			$c .= chr(0x80 | ($val % 64));
		}
	}
	return $c;
}

?>

<?php
function strSafeHtml($text) {
	$text = str_replace("\\\\", "\\", $text);
	$text = str_replace("\\\"", "\"", $text);
	$text = str_replace("\\'", "'", $text);
	$text = htmlentities($text);
	$text = preg_replace_callback("/%u[0-9A-Za-z]{4}/", toUtf8, $text);
	return $text;
}

function toUtf8($ar) {
	foreach($ar as $val) {
		$val = intval(substr($val, 2), 16);
		if ($val < 0x7F) {        // 0000-007F
			$c .= chr($val);
		} else if ($val < 0x800) { // 0080-0800
			$c .= chr(0xC0 | ($val / 64));
			$c .= chr(0x80 | ($val % 64));
		} else {                // 0800-FFFF
			$c .= chr(0xE0 | (($val / 64) / 64));
			$c .= chr(0x80 | (($val / 64) % 64));
			$c .= chr(0x80 | ($val % 64));
		}
	}
	return $c;
}

?>

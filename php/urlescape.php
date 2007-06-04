<?php
function strSafeHtml($text) {
	$text = str_replace("\\\\", "\\", $text);
	$text = str_replace("\\\"", "\"", $text);
	$text = str_replace("\\'", "'", $text);
	$text = htmlentities($text);
	$text = preg_replace_callback("/%u[0-9A-Za-z]{4}/", toUtf8, $text);
	return $text;
}

function urlescape($str) {
	preg_match_all("/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e", $str, $r);

	// 匹配utf-8字符，
	$str = $r[0];
	$l = count($str);
	for ($i=0 ; $i<$l ; $i++) {
		$value = ord($str[$i][0]);
/*
   if($value >= 192 && $value <= 223) $i++;//單字節
   elseif($value >= 224 && $value <= 239) $i = $i + 2;//雙字節
   elseif($value >= 240 && $value <= 247) $i = $i + 3;//三字節
//*/
		if ($value < 223) {
			$str[$i] = rawurlencode(utf8_decode($str[$i]));
			//先將utf8編碼轉換為ISO-8859-1編碼的單字節字符，urlencode單字節字符.
			//utf8_decode()的作用相當於iconv("UTF-8","CP1252",$v)。
		} else {
			$str[$i] = "%u".
				strtoupper(
					bin2hex(
						strrev(
							iconv("UTF-8", "UCS-2", $str[$i])
						)
					)
				);
		}
	}
	return join("",$str);
}

function urlunescape($str) {
	$str = preg_replace_callback("/%u[0-9A-Za-z]{4}/", toUtf8, $str);
	return $str;
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

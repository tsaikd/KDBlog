<?php
/*
flag:
	0x01: read
	0x02: write
	0x04: try to create if not exists
*/
function check_necessary_dir($cname, $flag) {
	global $BLOGCONF;
	global $BLOGLANG;
	$path = $BLOGCONF[$cname];

	if ($flag & 0x04) {
		if (!file_exists($path)) {
			include_once("php/mkdir_ex.php");

			@mkdir_ex($path) or                                                                 die("'".$cname."' ".$BLOGLANG["message"]["cannotmake"].", ".$BLOGLANG["message"]["checkconf"]);
		}
	}

	if ($flag & 0x01) {
		is_readable($path) or
			die("'".$cname."' ".$BLOGLANG["message"]["cannotread"].", ".$BLOGLANG["message"]["checkconf"]);
	}

	if ($flag & 0x02) {
		is_writable($path) or
			die("'".$cname."' ".$BLOGLANG["message"]["cannotwrite"].", ".$BLOGLANG["message"]["checkconf"]);
	}
}

?>

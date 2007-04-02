<?php
function logHtaccess() {
	global $BLOGCONF;
	$flag = 0x01;

	logecho("<IfModule mod_rewrite.c>\n", $flag);
	logecho("\tRewriteEngine On\n", $flag);
	logecho("\tRewriteBase ".$BLOGCONF["blogurl"]["blog"]."\n", $flag);
	logecho("\tRewriteRule ^data/([0-9]+)/([0-9]+)/([0-9]+_[0-9]+\.xml)$ ?fpath=data/$1/$2/$3\n", $flag);
	logecho("\tRewriteRule ^searchbot/$ data.php?ftype=searchbot\n", $flag);
	logecho("\tRewriteRule ^sitemap\.xml$ data.php?ftype=sitemap\n", $flag);
	logecho("</IfModule>\n", $flag);
}

?>

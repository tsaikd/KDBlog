<?php
/*
$start_time[0] = time();
$start_time[1] = (double)microtime();
*/

header('Content-type: text/html; charset=utf-8');

if (!file_exists("config.php")) {
	echo "Please setup your config file first!\n";
	echo "See 'config.php.example' for more information.\n";
	exit;
}
include_once("config.php");
if ($BLOGCONF["version"] < 11) {
	echo $BLOGLANG["message"]["confTooOld"];
	exit;
}

# Check server state
include_once("php/check_necessary_dir.php");
check_necessary_dir("cachpath", 0x07);
check_necessary_dir("datapath", 0x01);
check_necessary_dir("tagspath", 0x07);
check_necessary_dir("cmntpath", 0x07);
check_necessary_dir("specpath", 0x01);
check_necessary_dir($BLOGCONF["func"]["comment"]["indexByTime"], 0x0F);

# Check .htaccess to rewrite url
if (!file_exists(".htaccess")) {
	include_once("php/logHtaccess.php");
	if (is_writeable(".")) {
		$logfp = fopen(".htaccess", "w");
		logHtaccess();
		fclose($logfp);
		unset($logfp);
	} else {
		$fpath = $BLOGCONF["cachpath"]."/htaccess.cache";
		$logfp = fopen($fpath, "w");
		printf($BLOGLANG["message"]["error"].": ".$BLOGLANG["server"]["movehtaccess"]
			, $fpath
			, dirname($_SERVER["SCRIPT_FILENAME"])."/.htaccess"
		);
		logHtaccess();
		fclose($logfp);
		unset($logfp);
		exit();
	}
}

# Check need to clean cache or not
$name = "cleanCache";
if (is_state_old($name)) {
	cleanCache();
	touch_state_file($name);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?=$BLOGCONF["title"]?></title>
		<base href="<?=$BLOGCONF["link"]?>">
		<link rel="alternate" type="application/rss+xml" title="<?=$BLOGCONF["title"]?>" href="rss2.php?feed=all">
		<script type="text/javascript">
blog = {};
blog.lang = {};

blog.lang.button = {};
blog.lang.button.submit = "<?=$BLOGLANG["button"]["submit"]?>";

blog.lang.article = {};
blog.lang.article.toolbar = {};
blog.lang.article.toolbar.close = "<?=$BLOGLANG["article"]["toolbar"]["close"]?>";
blog.lang.article.toolbar.fold = "<?=$BLOGLANG["article"]["toolbar"]["fold"]?>";
blog.lang.article.toolbar.unfold = "<?=$BLOGLANG["article"]["toolbar"]["unfold"]?>";
blog.lang.article.toolbar.permalink = "<?=$BLOGLANG["article"]["toolbar"]["permalink"]?>";
blog.lang.article.toolbar.comment = "<?=$BLOGLANG["article"]["toolbar"]["comment"]?>";

blog.lang.article.tags = "<?=$BLOGLANG["article"]["tags"]?>";
blog.lang.article.loading = "<?=$BLOGLANG["article"]["loading"]?>";
blog.lang.article.notitle = "<?=$BLOGLANG["article"]["notitle"]?>";

blog.lang.special = {};
blog.lang.special.runSpecOk = "<?=$BLOGLANG["special"]["runSpecOk"]?>";
blog.lang.special.runSpecError = "<?=$BLOGLANG["special"]["runSpecError"]?>";

blog.lang.comment = {};
blog.lang.comment.errmsg = {};
blog.lang.comment.errmsg.multiComment = "<?=$BLOGLANG["comment"]["errmsg"]["multiComment"]?>";

blog.conf = {};
blog.conf.link = "<?=$BLOGCONF["link"]?>";
blog.conf.currentArticle = null;

blog.conf.func = {};
blog.conf.init = null;

blog.conf.func.comment = {};
blog.conf.func.comment.enable = <?=$BLOGCONF["func"]["comment"]["enable"]?"true":"false"?>;
blog.conf.func.comment.img_num = 0;

if (!Array.prototype.indexOf) { // for IE6
	Array.prototype.indexOf = function(val, fromIndex) {
		if (typeof(fromIndex) != 'number') fromIndex = 0;
		for (var index = fromIndex,len = this.length; index < len; index++)
			if (this[index] == val) return index;
		return -1;
	}
}
		</script>
<?php
include_once("php/indexHeader.php");

if (file_exists("favicon.ico"))
	echo '<link rel="SHORTCUT ICON" href="favicon.ico" type="image/x-icon">'."\n";

getCacheIndex("cssInside");
getCacheIndex("jsInside");
?>
		<script type="text/javascript">
blog.conf.init = function () {
	chgMenuTag("menutab_All");

	var showText = "";
	var showObj;
	showObj = document.getElementById("displayArea");

<?php
if (!$_REQUEST["fpath"]) {
	include_once("php/getRecentArticlePath.php");
	$darray = getRecentArticlePath($BLOGCONF["datapath"], $BLOGCONF["numAtStart"]);
	for ($i=0 ; $i<count($darray) ; $i++)
		$darray[$i] = "data".substr($darray[$i], strlen($BLOGCONF["datapath"]));

	foreach ($darray as $val)
		echo "showText += \"<div class='article' id='\"+getIdFromPath(\"$val\")+\"' onmouseover='javascript:selectArticle(this)'><\\/div>\";\n";
	echo "showObj.innerHTML = showText;\n";
	foreach ($darray as $val)
		echo "showArticle(\"$val\", 0x30);\n";
}
?>
}
		</script>
	</head>
	<body>
		<div id="header"><a onfocus='this.blur()' class='title' href="<?=$BLOGCONF["link"]?>"><?=$BLOGCONF["title"]?></a><span class="subtitle"><?=$BLOGCONF["description"]?></span></div>
		<div id="mainmenu">
			<div id="menuOpt">
<?php if ($BLOGCONF["func"]["google"]["enable"]) : ?>
<form class="googleForm" target="_blank" method="get" action="http://www.google.com/search">
<input class="googleOpt" type="checkbox" name="sitesearch" value="<?=$BLOGCONF["blogurl"]["sitesearch"]?>" checked /><?=$BLOGLANG["mainmenu"]["menuOpt"]["googleOpt"]?><br />
<input class="googleInput" type="text" name="q" />
<input class="googleSubmit" type="submit" value="Google" title="<?=$BLOGLANG["message"]["runNewWin"]?>" onfocus="javascript:this.blur()" />
</form>
<?php endif ?>
				<a onfocus='javascript:this.blur()' href="javascript:closeArticle('displayArea')"><?=$BLOGLANG["mainmenu"]["menuOpt"]["closeAll"]?></a>
			</div>
			<div id="mainmenuTabs">
				<a onfocus='javascript:this.blur()' id="menutab_All" class="menutab" href="javascript:chgMenuTag('menutab_All')"><?=$BLOGLANG["mainmenu"]["mainmenuTabs"]["menutab_All"]?></a>
				<a onfocus='javascript:this.blur()' id="menutab_Tags" class="menutab" href="javascript:chgMenuTag('menutab_Tags')"><?=$BLOGLANG["mainmenu"]["mainmenuTabs"]["menutab_Tags"]?></a>
				<a onfocus='javascript:this.blur()' id="menutab_Spec" class="menutab" href="javascript:chgMenuTag('menutab_Spec')"><?=$BLOGLANG["mainmenu"]["mainmenuTabs"]["menutab_Spec"]?></a>
			</div>
			<div id="menutabContents"></div>
			<div id="menures">
<?php
include_once("php/getRecentCommentPath.php");
$farray = getRecentCommentPath($BLOGCONF["func"]["comment"]["showNum"]);
if (count($farray)) {
	echo $BLOGLANG["mainmenu"]["menures"]["cmntidx"].":<br />";
	foreach ($farray as $f) {
		$fdir = dirname($f);
		$fname = basename($f);
		$buf = explode("_", $fname);
		$dataVPath = "data/$fdir/".$buf[0]."_".$buf[1].".xml";
		echo "<a onfocus='javascript:this.blur()' class='menuday' href='javascript:showArticle(\"$dataVPath\", 1)'>".substr($dataVPath, 5, -4)." (".(int)$buf[2].")</a><br />";
	}
	echo "<hr />";
}
?>
				<a onfocus='javascript:this.blur()' href="rss2.php?feed=all"><?php
if (file_exists($BLOGCONF["rss2AllImg"]))
	echo "<img alt='".$BLOGLANG["mainmenu"]["menures"]["rss2All"]."' src='".$BLOGCONF["rss2AllImg"]."' />";
else
	echo $BLOGLANG["mainmenu"]["menures"]["rss2All"];
?></a><br /><br /><?php
if ($BLOGCONF["func"]["showLastDate"]["enable"]) {
	echo "<span class='lastDate'>";

	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath($BLOGCONF["datapath"], 1);
	include_once("php/transPath.php");
	$lastDate = transPath2Date($farray[0]);
	echo $BLOGLANG["mainmenu"]["menures"]["lastDate"].": ".$lastDate;

	echo "</span><br />";
}
if ($BLOGCONF["func"]["version"]["enable"])
	echo "<span class='version'>KDBlog rev".$BLOGCONF["version"]."</span><br />";
if ($BLOGCONF["func"]["searchbot"]["enable"])
	echo "<a href='data.php?ftype=searchbot' style='display: none;'>search bot only</a>";
?>
			</div>
		</div>
		<div id="displayArea"><?
if ($_REQUEST["fpath"]) {
	$darray = explode(",", $_REQUEST["fpath"]);
	$darray = array_unique($darray);

	include_once("php/showArticle.php");
	foreach ($darray as $val)
		getCacheArticle($val, "html");
}
?></div>
		<script type="text/javascript">
			if (blog.conf.init)
				blog.conf.init();
		</script>
<?php
if ($BLOGCONF["extraFooter"])
	foreach ($BLOGCONF["extraFooter"] as $f)
		include($f);
?>
<?php
/*
$stop_time[0] = time();
$stop_time[1] = (double)microtime();
printf("PHP use %d + %f sec", $stop_time[0]-$start_time[0], $stop_time[1]-$start_time[1]);
*/
?>
	</body>
</html>

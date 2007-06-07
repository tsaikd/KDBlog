<?php
if ($CONF["func"]["debug"]["enable"]) {
	$start_time[0] = time();
	$start_time[1] = (double)microtime();
}

header('Content-type: text/html; charset=utf-8');

if (!file_exists("config.php")) {
	echo "Please setup your config file first!\n";
	echo "See 'config.php.example' for more information.\n";
	exit;
}
include_once("config.php");
if ($CONF["version"] < 17)
	die($LANG["message"]["confTooOld"]);

# Check server state
include_once("php/check_necessary_dir.php");
check_necessary_dir("cache", 0x07);
check_necessary_dir("data", 0x01);
check_necessary_dir("tags", 0x07);
check_necessary_dir("comment", 0x07);
check_necessary_dir("spec", 0x01);
check_necessary_dir($CONF["func"]["comment"]["indexByTime"], 0x0F);

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
		<title><?php
echo $CONF["title"];
if ($_REQUEST["fpath"]) {
	$darray = explode(",", $_REQUEST["fpath"]);
	$darray = array_unique($darray);

	include_once("php/getArticleTitle.php");
	echo " - ".getArticleTitle($darray[0]);
}
?></title>
		<base href="<?=$CONF["link"]?>">
<?php if (file_exists("favicon.ico")) : ?>
		<link rel="SHORTCUT ICON" type="image/x-icon" href="favicon.ico">
<?php endif ?>
		<link rel="alternate" type="application/rss+xml" title="<?=$CONF["title"]?>" href="rss2.php?feed=all">
		<link rel="stylesheet" type="text/css" href="data.php?ftype=cssInside">
		<script type="text/javascript" src="data.php?ftype=jsInside"></script>
		<script type="text/javascript">
lang = {};

lang.button = {};
lang.button.submit = "<?=$LANG["button"]["submit"]?>";

lang.article = {};
lang.article.toolbar = {};
lang.article.toolbar.close = "<?=$LANG["article"]["toolbar"]["close"]?>";
lang.article.toolbar.fold = "<?=$LANG["article"]["toolbar"]["fold"]?>";
lang.article.toolbar.unfold = "<?=$LANG["article"]["toolbar"]["unfold"]?>";
lang.article.toolbar.permalink = "<?=$LANG["article"]["toolbar"]["permalink"]?>";
lang.article.toolbar.comment = "<?=$LANG["article"]["toolbar"]["comment"]?>";

lang.article.tags = "<?=$LANG["article"]["tags"]?>";
lang.article.loading = "<?=$LANG["article"]["loading"]?>";
lang.article.notitle = "<?=$LANG["article"]["notitle"]?>";

lang.special = {};
lang.special.runSpecOk = "<?=$LANG["special"]["runSpecOk"]?>";
lang.special.runSpecError = "<?=$LANG["special"]["runSpecError"]?>";

lang.comment = {};
lang.comment.write = {};
lang.comment.write.notify = "<?=$LANG["comment"]["write"]["notify"]?>";
lang.comment.write.validTags = "<?=$LANG["comment"]["write"]["validTags"]?>";
lang.comment.errmsg = {};
lang.comment.errmsg.multiComment = "<?=$LANG["comment"]["errmsg"]["multiComment"]?>";

conf = {};
conf.link = "<?=$CONF["link"]?>";
conf.currentArticle = null;

conf.func = {};
conf.init = null;

conf.func.comment = {};
conf.func.comment.enable = <?=$CONF["func"]["comment"]["enable"]?"true":"false"?>;
conf.func.comment.notify = <?=$CONF["func"]["commentNotify"]["enable"]?"true":"false"?>;
conf.func.comment.img_num = 0;

if (!Array.prototype.indexOf) { // for IE6
	Array.prototype.indexOf = function(val, fromIndex) {
		if (typeof(fromIndex) != 'number') fromIndex = 0;
		for (var index = fromIndex,len = this.length; index < len; index++)
			if (this[index] == val) return index;
		return -1;
	}
}

conf.init = function () {
	chgMenuTag("menutab_Recent");

	var showText = "";
	var showObj;
	showObj = document.getElementById("displayArea");

<?php
if (!$_REQUEST["fpath"]) {
	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath($CONF["numAtStart"]);
	foreach ($farray as $vpath)
		echo "showText += \"<div class='article' id='\"+getIdFromPath(\"$vpath\")+\"' onmouseover='javascript:selectArticle(this)'><\\/div>\";\n";
	echo "showObj.innerHTML = showText;\n";
	foreach ($farray as $vpath)
		echo "showArticle(\"$vpath\", 0x30);\n";
}
?>
}
		</script>
	</head>
	<body>
		<div id="header"><a onfocus='this.blur()' class='title' href="<?=$CONF["link"]?>"><?=$CONF["title"]?></a><span class="subtitle"><?=$CONF["description"]?></span></div>
		<div id="mainmenu">
			<div id="menuOpt">
<?php if ($CONF["func"]["google"]["enable"]) : ?>
<form class="googleForm" target="_blank" method="get" action="http://www.google.com/search">
<input class="googleOpt" type="checkbox" name="sitesearch" value="<?=$CONF["blogurl"]["sitesearch"]?>" checked /><?=$LANG["mainmenu"]["menuOpt"]["googleOpt"]?><br />
<input class="googleInput" type="text" name="q" />
<input class="googleSubmit" type="submit" value="Google" title="<?=$LANG["message"]["runNewWin"]?>" onfocus="javascript:this.blur()" />
</form>
<?php endif ?>
				<a onfocus='javascript:this.blur()' href="javascript:closeArticle('displayArea')"><?=$LANG["mainmenu"]["menuOpt"]["closeAll"]?></a>
			</div>
			<div id="mainmenuTabs">
				<a onfocus='javascript:this.blur()' id="menutab_Recent" class="menutab" href="javascript:chgMenuTag('menutab_Recent')"><?=$LANG["mainmenu"]["mainmenuTabs"]["menutab_Recent"]?></a>
				<a onfocus='javascript:this.blur()' id="menutab_All" class="menutab" href="javascript:chgMenuTag('menutab_All')"><?=$LANG["mainmenu"]["mainmenuTabs"]["menutab_All"]?></a>
				<a onfocus='javascript:this.blur()' id="menutab_Tags" class="menutab" href="javascript:chgMenuTag('menutab_Tags')"><?=$LANG["mainmenu"]["mainmenuTabs"]["menutab_Tags"]?></a>
				<a onfocus='javascript:this.blur()' id="menutab_Spec" class="menutab" href="javascript:chgMenuTag('menutab_Spec')"><?=$LANG["mainmenu"]["mainmenuTabs"]["menutab_Spec"]?></a>
			</div>
			<div id="menutabContents"></div>
			<div id="menures">
<?php
include_once("php/getRecentCommentPath.php");
$farray = getRecentCommentPath($CONF["func"]["comment"]["showNum"]);
if (count($farray)) {
	echo "<div class='menublock'>";
	echo "<div class='menuitem'>".$LANG["mainmenu"]["menures"]["cmntidx"].":</div>";
	foreach ($farray as $f) {
		$fdir = dirname($f);
		$fname = basename($f);
		$buf = explode("_", $fname);
		$dataVPath = "data/$fdir/".$buf[0]."_".$buf[1].".xml";
		echo "<a class='menuitem'";
		echo " onfocus='javascript:this.blur()'";
		echo " href='javascript:showArticle(\"$dataVPath\", 1)'>";
		echo substr($dataVPath, 5, -4)." (".(int)$buf[2].")";
		echo "</a><br />";
	}
	echo "</div>";
}

if ($CONF["extraMenures"])
	foreach ($CONF["extraMenures"] as $f)
		include($f);
?>
				<a class='menuitem' onfocus='javascript:this.blur()' href="rss2.php?feed=all"><?php
if (file_exists($CONF["rss2AllImg"]))
	echo "<img alt='".$LANG["mainmenu"]["menures"]["rss2All"]."' src='".$CONF["rss2AllImg"]."' />";
else
	echo $LANG["mainmenu"]["menures"]["rss2All"];
?></a><br /><br /><?php
if ($CONF["func"]["showLastDate"]["enable"]) {
	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath(1);
	if (count($farray)) {
		include_once("php/transPath.php");
		$lastDate = transPath2Date($farray[0]);
		echo "<span class='lastDate'>";
		echo $LANG["mainmenu"]["menures"]["lastDate"].": ".$lastDate;
		echo "</span><br />";
	}
}
if ($CONF["func"]["version"]["enable"])
	echo "<span class='version'>KDBlog rev".$CONF["version"]."</span><br />";
if ($CONF["func"]["searchbot"]["enable"])
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
<?php
if ($CONF["extraFooter"])
	foreach ($CONF["extraFooter"] as $f)
		include($f);

if ($CONF["func"]["debug"]["enable"]) {
	$stop_time[0] = time();
	$stop_time[1] = (double)microtime();
	printf("PHP use %d + %f sec",
		$stop_time[0]-$start_time[0],
		$stop_time[1]-$start_time[1]);
}
?>
		<script type="text/javascript">
			if (conf.init)
				conf.init();
		</script>
	</body>
</html>

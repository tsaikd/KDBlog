<?php
if ($BLOGCONF["func"]["debug"]["enable"]) {
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
if ($BLOGCONF["version"] < 15)
	die($BLOGLANG["message"]["confTooOld"]);

# Check server state
include_once("php/check_necessary_dir.php");
check_necessary_dir("cachpath", 0x07);
check_necessary_dir("datapath", 0x01);
check_necessary_dir("tagspath", 0x07);
check_necessary_dir("cmntpath", 0x07);
check_necessary_dir("specpath", 0x01);
check_necessary_dir($BLOGCONF["func"]["comment"]["indexByTime"], 0x0F);

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
echo $BLOGCONF["title"];
if ($_REQUEST["fpath"]) {
	$darray = explode(",", $_REQUEST["fpath"]);
	$darray = array_unique($darray);

	include_once("php/getArticleTitle.php");
	echo " - ".getArticleTitle($darray[0]);
}
?></title>
		<base href="<?=$BLOGCONF["link"]?>">
<?php if (file_exists("favicon.ico")) : ?>
		<link rel="SHORTCUT ICON" type="image/x-icon" href="favicon.ico">
<?php endif ?>
		<link rel="alternate" type="application/rss+xml" title="<?=$BLOGCONF["title"]?>" href="rss2.php?feed=all">
		<link rel="stylesheet" type="text/css" href="data.php?ftype=cssInside">
		<script type="text/javascript" src="data.php?ftype=jsInside"></script>
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
blog.lang.comment.write = {};
blog.lang.comment.write.notify = "<?=$BLOGLANG["comment"]["write"]["notify"]?>";
blog.lang.comment.errmsg = {};
blog.lang.comment.errmsg.multiComment = "<?=$BLOGLANG["comment"]["errmsg"]["multiComment"]?>";

blog.conf = {};
blog.conf.link = "<?=$BLOGCONF["link"]?>";
blog.conf.currentArticle = null;

blog.conf.func = {};
blog.conf.init = null;

blog.conf.func.comment = {};
blog.conf.func.comment.enable = <?=$BLOGCONF["func"]["comment"]["enable"]?"true":"false"?>;
blog.conf.func.comment.notify = <?=$BLOGCONF["func"]["commentNotify"]["enable"]?"true":"false"?>;
blog.conf.func.comment.img_num = 0;

if (!Array.prototype.indexOf) { // for IE6
	Array.prototype.indexOf = function(val, fromIndex) {
		if (typeof(fromIndex) != 'number') fromIndex = 0;
		for (var index = fromIndex,len = this.length; index < len; index++)
			if (this[index] == val) return index;
		return -1;
	}
}

blog.conf.init = function () {
	chgMenuTag("menutab_Recent");

	var showText = "";
	var showObj;
	showObj = document.getElementById("displayArea");

<?php
if (!$_REQUEST["fpath"]) {
	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath($BLOGCONF["numAtStart"]);
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
				<a onfocus='javascript:this.blur()' id="menutab_Recent" class="menutab" href="javascript:chgMenuTag('menutab_Recent')"><?=$BLOGLANG["mainmenu"]["mainmenuTabs"]["menutab_Recent"]?></a>
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
	echo "<div class='menublock'>";
	echo "<div class='menuitem'>".$BLOGLANG["mainmenu"]["menures"]["cmntidx"].":</div>";
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

if ($BLOGCONF["extraMenures"])
	foreach ($BLOGCONF["extraMenures"] as $f)
		include($f);
?>
				<a class='menuitem' onfocus='javascript:this.blur()' href="rss2.php?feed=all"><?php
if (file_exists($BLOGCONF["rss2AllImg"]))
	echo "<img alt='".$BLOGLANG["mainmenu"]["menures"]["rss2All"]."' src='".$BLOGCONF["rss2AllImg"]."' />";
else
	echo $BLOGLANG["mainmenu"]["menures"]["rss2All"];
?></a><br /><br /><?php
if ($BLOGCONF["func"]["showLastDate"]["enable"]) {
	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath(1);
	if (count($farray)) {
		include_once("php/transPath.php");
		$lastDate = transPath2Date($farray[0]);
		echo "<span class='lastDate'>";
		echo $BLOGLANG["mainmenu"]["menures"]["lastDate"].": ".$lastDate;
		echo "</span><br />";
	}
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
<?php
if ($BLOGCONF["extraFooter"])
	foreach ($BLOGCONF["extraFooter"] as $f)
		include($f);

if ($BLOGCONF["func"]["debug"]["enable"]) {
	$stop_time[0] = time();
	$stop_time[1] = (double)microtime();
	printf("PHP use %d + %f sec",
		$stop_time[0]-$start_time[0],
		$stop_time[1]-$start_time[1]);
}
?>
		<script type="text/javascript">
			if (blog.conf.init)
				blog.conf.init();
		</script>
	</body>
</html>

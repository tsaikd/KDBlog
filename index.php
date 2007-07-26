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
if ($CONF["version"] < 20)
	die($LANG["message"]["confTooOld"]);

# Get last article path
include_once("php/getRecentArticlePath.php");
$farray = getRecentArticlePath(1);
$lastArticlePath = $farray[0];
unset($farray);

# Send Expires header
include_once("php/transPath.php");
if (!$CONF["func"]["debug"]["enable"]) {
	if ($_REQUEST["fpath"]) {
		$fpath = transPathV2R($_REQUEST["fpath"]);
		if (is_file($fpath))
			sendModHeader($fpath);
	} else {
		if (isset($lastArticlePath))
			sendModHeader(transPathV2R($lastArticlePath), 600);
		else
			sendModHeader(__FILE__, 600);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="<?=$CONF["langtype"]["html"]?>">
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
<?php if ($CONF["func"]["google"]["analytics"]["enable"]) : ?>
		<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
		<script type="text/javascript">
_uacct = "<?=$CONF["func"]["google"]["analytics"]["uacct"]?>";
urchinTracker();
		</script>
<?php endif ?>
		<script type="text/javascript">
isMSIE = /*@cc_on!@*/false;

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

conf.blogurl = {};
conf.blogurl.blog = "<?=$CONF["blogurl"]["blog"]?>";

conf.func = {};

conf.func.comment = {};
conf.func.comment.enable = <?=$CONF["func"]["comment"]["enable"]?"true":"false"?>;
conf.func.comment.notify = <?=$CONF["func"]["commentNotify"]["enable"]?"true":"false"?>;
conf.func.comment.img_num = 0;

conf.func.google = {};
conf.func.google.analytics = {};
conf.func.google.analytics.enable = false;

if (!Array.prototype.indexOf) { // for IE6
	Array.prototype.indexOf = function(val, fromIndex) {
		if (typeof(fromIndex) != 'number') fromIndex = 0;
		for (var index = fromIndex,len = this.length; index < len; index++)
			if (this[index] == val) return index;
		return -1;
	}
}

conf.init = function () {
	SetCookie("hl", "<?=$CONF["language"]?>");
	chgMenuTag("menutab_Recent");

	var buf = "";
	var obj;

	if (isMSIE) {
		// a strange bug in IE6
		obj = document.getElementById("menuOpt");
		obj.style.paddingTop = "0";
	}

	obj = document.getElementById("displayArea");
<?php
if (!isset($_REQUEST["fpath"])) {
	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath($CONF["numAtStart"]);
	foreach ($farray as $vpath)
		echo "buf += \"<div class='article' id='\"+getIdFromPath(\"$vpath\")+\"' onmouseover='javascript: selectArticle(this);'><\\/div>\";\n";
	echo "obj.innerHTML = buf;\n";
	foreach ($farray as $vpath)
		echo "showArticle(\"$vpath\", 0x30);\n";
}
?>
}

// sometimes browser will run undefined function selectArticle
// so use the empty function to avoid the problem
if (typeof(selectArticle) == undefined) {
	function selectArticle(obj) {}
}
		</script>
	</head>
	<body>
		<div id="header">
			<a class="lang" href="javascript:;" onclick="javascript: toggleObj(this.nextSibling, 'block');">Language</a><div class="langblock">
<?php
foreach (getDir("lang") as $f) {
	if (substr($f, -4) == ".php") {
		$f = substr($f, 0, -4);
		echo "<a class='langbtn' href='javascript: chgLang(\"$f\");'>$f</a>\n";
	}
}
?>
			</div><br />
			<a class="title" href="<?=$CONF["link"]?>"><?=$CONF["title"]?></a>
			<span class="subtitle"><?=$CONF["description"]?></span>
		</div>
		<div id="mainmenu">
			<div id="menuOpt" class="menublock">
<?php if ($CONF["func"]["google"]["search"]["enable"]) : ?>
<form class="googleForm" target="_blank" method="get" action="http://www.google.com/search">
<input class="googleOpt" type="checkbox" name="sitesearch" value="<?=$CONF["blogurl"]["sitesearch"]?>" checked /><?=$LANG["mainmenu"]["menuOpt"]["googleOpt"]?><br />
<input class="googleInput" type="text" name="q" />
<input class="googleSubmit" type="submit" value="Google" title="<?=$LANG["message"]["runNewWin"]?>" />
</form>
<?php endif ?>
				<a href="javascript:closeArticle('displayArea')"><?=$LANG["mainmenu"]["menuOpt"]["closeAll"]?></a>
			</div>
			<div id="mainmenuTabs">
				<a id="menutab_Recent" class="menutab" href="javascript:chgMenuTag('menutab_Recent')"><?=$LANG["mainmenu"]["mainmenuTabs"]["menutab_Recent"]?></a>
				<a id="menutab_All" class="menutab" href="javascript:chgMenuTag('menutab_All')"><?=$LANG["mainmenu"]["mainmenuTabs"]["menutab_All"]?></a>
				<a id="menutab_Tags" class="menutab" href="javascript:chgMenuTag('menutab_Tags')"><?=$LANG["mainmenu"]["mainmenuTabs"]["menutab_Tags"]?></a>
				<a id="menutab_Spec" class="menutab" href="javascript:chgMenuTag('menutab_Spec')"><?=$LANG["mainmenu"]["mainmenuTabs"]["menutab_Spec"]?></a>
			</div>
			<div id="menutabContents"></div>
			<div id="menures">
<?php
include_once("php/getRecentCommentPath.php");
$farray = getRecentCommentPath($CONF["func"]["comment"]["showNum"]);
if (count($farray)) {
	include_once("php/getArticleTitle.php");
	echo "<div class='menublock'>";
	echo "<div class='menuitem'>".$LANG["mainmenu"]["menures"]["cmntidx"].":</div>";
	echo "<div class='menudir'>";
	foreach ($farray as $f) {
		$fdir = dirname($f);
		$fname = basename($f);
		$buf = explode("_", $fname);
		$dataVPath = "data/$fdir/".$buf[0]."_".$buf[1].".xml";
		echo "<a class='menuRecentFile'";
		echo " href='javascript:showArticle(\"$dataVPath\", 1)'>";
		echo "Re: ".getArticleTitle($dataVPath)." (".(int)$buf[2].")";
		echo "</a>";
	}
	echo "</div>";
	echo "</div>";
}

if ($CONF["extraMenures"])
	foreach ($CONF["extraMenures"] as $f)
		include($f);
?>
				<div class="menufootblock">
					<a class="menuitem" href="rss2.php?feed=all"><?php
if (file_exists($CONF["rss2AllImg"]))
	echo "<img alt='".$LANG["mainmenu"]["menures"]["rss2All"]."' src='".$CONF["rss2AllImg"]."' />";
else
	echo $LANG["mainmenu"]["menures"]["rss2All"];
?></a><br /><br /><?php
if ($CONF["func"]["showLastDate"]["enable"]) {
	if (isset($lastArticlePath)) {
		$lastDate = transPath2Date($lastArticlePath);
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

conf.func.google.analytics.enable = <?=$CONF["func"]["google"]["analytics"]["enable"]?"true":"false"?>;
		</script>
	</body>
</html>

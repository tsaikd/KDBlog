<?php
$start_time[0] = time();
$start_time[1] = (double)microtime();

if (!file_exists("config.php")) {
	echo "Please setup your config file first!\n";
	echo "See 'config.php.example' for more information.\n";
	exit;
}
include_once("config.php");
if ($BLOGCONF["version"] < 2) {
	echo $BLOGLANG["message"]["confTooOld"];
	exit;
}

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

			@mkdir_ex($path) or
				die("'".$cname."' ".$BLOGLANG["message"]["cannotmake"].", ".$BLOGLANG["message"]["checkconf"]);
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

# Checking server state
check_necessary_dir("cachpath", 0x07);
check_necessary_dir("datapath", 0x01);
check_necessary_dir("tagspath", 0x07);
check_necessary_dir("cmntpath", 0x07);
check_necessary_dir("specpath", 0x01);

$name = "cleanCache";
if (is_state_old($name)) {
	cleanCache();
	touch_state_file($name);
}

$name = "rebuildTags";
if (is_state_old($name)) {
	include_once("php/rm_ex.php");
	include_once("php/cleanDir.php");
	include_once("php/rebuildTags.php");
	cleanDir($BLOGCONF["tagspath"]);
	rebuildTags($BLOGCONF["datapath"], $BLOGCONF["tagspath"]);
	touch_state_file($name);
	touch_state_file("scanTags");
	rm_ex($BLOGCONF["cache"]["menutab_All"]["cachePath"]);
	rm_ex($BLOGCONF["cache"]["menutab_Tags"]["cachePath"]);
}

$name = "scanTags";
if (is_state_old($name)) {
	include_once("php/rm_ex.php");
	include_once("php/rebuildTags.php");
	rebuildTags($BLOGCONF["datapath"], $BLOGCONF["tagspath"]);
	touch_state_file($name);
	rm_ex($BLOGCONF["cache"]["menutab_All"]["cachePath"]);
	rm_ex($BLOGCONF["cache"]["menutab_Tags"]["cachePath"]);
}

session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?=$BLOGCONF["title"]?></title>
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
blog.lang.article.toolbar.comment = "<?=$BLOGLANG["article"]["toolbar"]["comment"]?>";

blog.lang.article.tags = "<?=$BLOGLANG["article"]["tags"]?>";
blog.lang.article.loading = "<?=$BLOGLANG["article"]["loading"]?>";
blog.lang.article.invalidData = "<?=$BLOGLANG["article"]["invalidData"]?>";
blog.lang.article.notitle = "<?=$BLOGLANG["article"]["notitle"]?>";

blog.lang.special = {};
blog.lang.special.runSpecOk = "<?=$BLOGLANG["special"]["runSpecOk"]?>";
blog.lang.special.runSpecError = "<?=$BLOGLANG["special"]["runSpecError"]?>";

blog.lang.comment = {};
blog.lang.comment.errmsg = {};
blog.lang.comment.errmsg.multiComment = "<?=$BLOGLANG["comment"]["errmsg"]["multiComment"]?>";

blog.conf = {};
blog.conf.currentArticle = null;

blog.conf.func = {};
blog.conf.func.comment = {};
blog.conf.func.comment.enable = <?=$BLOGCONF["func"]["comment"]["enable"]?"true":"false"?>;

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
include_once("php/getDir.php");

if (file_exists("favicon.ico"))
	echo '<link rel="SHORTCUT ICON" href="favicon.ico" type="image/x-icon" ></link>'."\n";

if (isCache("cssInside")) {
	getCache("cssInside");
} else {
	$path = "css";
	$farray = getDir($path);
	foreach ($farray as $f) {
		if (strtolower(substr($f, -4)) != ".css")
			continue;
		echo "<link href=\"$path/$f\" rel=\"stylesheet\" type=\"text/css\">\n";
	}
}

if (isCache("jsInside")) {
	getCache("jsInside");
} else {
	$path = "js";
	$farray = getDir($path);
	foreach ($farray as $f) {
		if (strtolower(substr($f, -3)) != ".js")
			continue;
		echo "<script src=\"$path/$f\" type=\"text/javascript\"></script>\n";
	}
}

?>
		<script type="text/javascript">
/*
position:
	0: "only" (default)
	1: "top"
	2: "bottom"

	0x10: noscroll
	0x20: force load
*/
function showArticle(fpath, position) {
	unSelectAllArticle();

	var node;
	var id = getIdFromPath(fpath);
	var showObj = document.getElementById(id);
	if (showObj == null) {
		showObj = document.getElementById("displayArea");
		node = document.createElement("div");
		node.setAttribute("class", "article");
		node.setAttribute("id", id);
		node.setAttribute("onmouseover", "selectArticle(this)");

		switch (position & 0x0F) {
		case 1:
			showObj.insertBefore(node, showObj.firstChild);
			break;
		case 2:
			showObj.appendChild(node);
			break;
		default:
			closeArticle();
			showObj.appendChild(node);
			break;
		}

		showObj = node;
	} else {
		position &= 0xF0;

		if (!(position & 0x20)) {
			scrollToArticle(showObj);
			selectArticle(showObj);
			return;
		}
	}

	var ajax = createAjax();
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 1) {
			showObj.innerHTML = "<div class='loading'>"+blog.lang.article.loading+"<\/div>";
		} else if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				var i, j;
				var buf;
				var bufNode;
				var xmldoc = ajax.responseXML;

				showObj.innerHTML = "";
				node = getArticleToolBar(id);
				if (node)
					showObj.appendChild(node);

				if (xmldoc.getElementsByTagName('article').length) {
					// show article menu (include: tags date)
					node = getArticleTagsMenu(xmldoc, fpath);
					if (node)
						showObj.appendChild(node);

					// show title
					node = getArticleTitle(xmldoc, fpath);
					if (node)
						showObj.appendChild(node);
					var title = getNodeText(node);

					// get macro info
					var macroBuf;
					var macroReplace = new Array();
					var macro = new Array();
					node = xmldoc.getElementsByTagName('macro');
					for (i=0 ; i<node.length ; i++) {
						bufNode = node[i];
						buf = bufNode.getAttribute("name");
						if (buf == "replace") {
							if (bufNode.childNodes.length < 2)
								continue;
							macroBuf = new Array();
							for (j=0 ; j<bufNode.childNodes.length ; j++) {
								buf = bufNode.childNodes[j];
								if (buf.nodeName == "from") {
									macroBuf[0] = (getNodeText(buf));
								} else if (buf.nodeName == "to") {
									macroBuf[1] = (getNodeText(buf));
								}
							}
							if (macroBuf.length == 2)
								macroReplace.push(macroBuf);
						} else {
							macro.push(buf);
						}
					}

					// show contents
					node = xmldoc.getElementsByTagName('contents')[0];
					if (node) {
						for (i=0 ; i<node.childNodes.length ; i++) {
							bufNode = node.childNodes[i];
							if (bufNode.nodeType == 1) {
								if (macro.indexOf(bufNode.nodeName) >= 0)
									parseMacroNode(xmldoc, bufNode);
							} else if (bufNode.nodeType == 3) {
								if (bufNode.data.length <= 1) {
									node.removeChild(bufNode);
									i--;
									continue;
								}

								for (j=0 ; j<macroReplace.length ; j++) {
									buf = bufNode.data.indexOf(macroReplace[j][0]);
									while (buf >= 0) {
										bufNode.replaceData(buf, macroReplace[j][0].length, macroReplace[j][1]);
										buf = bufNode.data.indexOf(macroReplace[j][0], buf+macroReplace[j][1].length);
									}
								}

								// because IE will modify data of DOM text
								macroBuf = xmldoc.createCDATASection(bufNode.data);
								node.replaceChild(macroBuf, bufNode);
							} else if (bufNode.nodeType == 4) {
								for (j=0 ; j<macroReplace.length ; j++) {
									buf = bufNode.data.indexOf(macroReplace[j][0]);
									while (buf >= 0) {
										bufNode.replaceData(buf, macroReplace[j][0].length, macroReplace[j][1]);
										buf = bufNode.data.indexOf(macroReplace[j][0], buf+macroReplace[j][1].length);
									}
								}
							}
						}

						bufNode = document.createElement("div");
						bufNode.setAttribute("class", "contents");
						// because IE will parse innerHTML
						bufNode.innerHTML = "<pre>"+getNodeXml(node, 0x01)+"<\/pre>";
						showObj.appendChild(bufNode);
					}

					// show comments
					node = getArticleComments(xmldoc);
					if (node)
						showObj.appendChild(node);

					// show run spec if it's a specFile
					node = xmldoc.getElementsByTagName('spectype')[0];
					if ((node) && (getNodeText(node) == "php")) {
						if (xmldoc.getElementsByTagName('code')[0]) {
							showObj.appendChild(document.createElement("br"));
							node = document.createElement("a");
							node.setAttribute("href", "javascript:runSpecFile('"+fpath+"')");
							node.innerHTML = title;
							showObj.appendChild(node);
						}
					}
				} else {
					showObj.innerHTML = "<div class='errorMsg'>"+blog.lang.article.invalidData+"<br \/>"+fpath+"<\/div>";
				}

				// without this stupid command, IE will display wrong contents
				// but Opera cannot use this line ...@_@
				if (navigator.appName != "Opera")
					showObj.innerHTML = showObj.innerHTML;

				if (!(position & 0x10)) {
					scrollToArticle(showObj);
					selectArticle(showObj);
				}
			} else {
				alert('There was a problem with the request.');
			}
		}
	}
	ajax.open("POST", "data.php", true);
	ajax.setRequestHeader("Content-Type", 
		"application/x-www-form-urlencoded; charset=utf-8");
	ajax.send("ftype=article&fpath="+fpath);
}
		</script>
	</head>
	<body>
		<div id="header"><a onfocus='this.blur()' class='title' href="<?=$BLOGCONF["link"]?>"><?=$BLOGCONF["title"]?></a><span class="subtitle"><?=$BLOGCONF["description"]?></span></div>
		<div id="mainmenu">
			<div id="menuOpt">
				<a onfocus='this.blur()' href="javascript:closeArticle('displayArea')"><?=$BLOGLANG["mainmenu"]["menuOpt"]["closeAll"]?></a>
			</div>
			<div id="mainmenuTabs">
				<a onfocus='this.blur()' id="menutab_All" class="menutab" href="javascript:chgMenuTag('menutab_All')"><?=$BLOGLANG["mainmenu"]["mainmenuTabs"]["menutab_All"]?></a>
				<a onfocus='this.blur()' id="menutab_Tags" class="menutab" href="javascript:chgMenuTag('menutab_Tags')"><?=$BLOGLANG["mainmenu"]["mainmenuTabs"]["menutab_Tags"]?></a>
				<a onfocus='this.blur()' id="menutab_Spec" class="menutab" href="javascript:chgMenuTag('menutab_Spec')"><?=$BLOGLANG["mainmenu"]["mainmenuTabs"]["menutab_Spec"]?></a>
			</div>
			<div id="menutabContents"></div>
			<div id="menures">
				<a href="rss2.php?feed=all"><?php
if (file_exists($BLOGCONF["rss2AllImg"]))
	echo "<img alt='".$BLOGLANG["mainmenu"]["menures"]["rss2All"]."' src='rss2.gif' />";
else
	echo $BLOGLANG["mainmenu"]["menures"]["rss2All"];
?></a><br />
			</div>
		</div>
		<div id="displayArea"></div>
		<script type="text/javascript">
function init() {
	chgMenuTag("menutab_All");

	var showText = "";
	var showObj;
	showObj = document.getElementById("displayArea");

<?php
include_once("php/getRecentArticlePath.php");

if ($_REQUEST["fpath"]) {
	$darray = explode(",", $_REQUEST["fpath"]);
	$darray = array_unique($darray);
} else {
	$darray = getRecentArticlePath($BLOGCONF["datapath"], $BLOGCONF["numAtStart"]);
}

for ($i=0 ; $i<count($darray) ; $i++)
	$darray[$i] = "data".substr($darray[$i], strlen($BLOGCONF["datapath"]));
foreach ($darray as $val)
	echo "showText += \"<div class='article' id='\"+getIdFromPath(\"$val\")+\"' onmouseover='javascript:selectArticle(this)'><\\/div>\";\n";
echo "showObj.innerHTML = showText;\n";
foreach ($darray as $val)
	echo "showArticle(\"$val\", 0x30);\n";
?>
}

init();
		</script>
	</body>
</html>

<?php
$stop_time[0] = time();
$stop_time[1] = (double)microtime();

//printf("PHP use %d + %f sec", $stop_time[0]-$start_time[0], $stop_time[1]-$start_time[1]);

?>

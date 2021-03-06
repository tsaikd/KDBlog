<?php
function getArticleToolBar($id) {
	include_once("php/transPath.php");

	global $CONF;
	global $LANG;
	$vpath = transPathId2V($id);
	$res = "<div name='toolbar' class='toolbar'>";

	if ($CONF["func"]["comment"]["enable"])
	if (substr($id, 0, 7) != "special")
	$res .= "<a name='comment' class='button' href='javascript:commentArticle(\"".$id."\")'>".$LANG["article"]["toolbar"]["comment"]."</a> ";

	$res .= "<a name='permalink' class='button' href='index.php?fpath=".$vpath."'>".$LANG["article"]["toolbar"]["permalink"]."</a> ";

	$res .= "<a name='fold' class='button' href='javascript:foldArticle(\"".$id."\")'>".$LANG["article"]["toolbar"]["fold"]."</a> ";

	$res .= "<a name='close' class='button' href='javascript:closeArticle(\"".$id."\")'>".$LANG["article"]["toolbar"]["close"]."</a>";

	$res .= "</div>";
	return $res;
}
?>

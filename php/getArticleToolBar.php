<?php
function getArticleToolBar($id) {
	global $BLOGCONF;
	global $BLOGLANG;
	$res = "<div name='toolbar' class='toolbar'>";

	if ($BLOGCONF["func"]["comment"]["enable"])
	if (substr($id, 0, 7) != "special")
	$res .= "<a name='comment' onfocus='this.blur()' class='button' href='javascript:commentArticle(\"".$id."\")'>".$BLOGLANG["article"]["toolbar"]["comment"]."</a> ";

	$res .= "<a name='fold' onfocus='this.blur()' class='button' href='javascript:foldArticle(\"".$id."\")'>".$BLOGLANG["article"]["toolbar"]["fold"]."</a> ";

	$res .= "<a name='close' onfocus='this.blur()' class='button' href='javascript:closeArticle(\"".$id."\")'>".$BLOGLANG["article"]["toolbar"]["close"]."</a>";

	$res .= "</div>";
	return $res;
}
?>

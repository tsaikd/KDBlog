<!-- Google CSE code begins -->
<form class="googleForm" id="searchbox_<?=$CONF["func"]["google"]["cse"]["cseid"]?>" onsubmit="return false;">
  <input class="googleInput" type="text" name="q"/>
  <input class="googleSubmit" type="submit" value="Google"/>
</form>
<script src="http://www.google.com/coop/cse/brand?form=searchbox_<?=$CONF["func"]["google"]["cse"]["cseid"]?>"></script>

<div id="results_<?=$CONF["func"]["google"]["cse"]["cseid"]?>" style="display:none">
  <div class="cse-closeResults"> 
    <a>&times; <?=$LANG["article"]["toolbar"]["close"]?></a>
  </div>
  <div class="cse-resultsContainer"></div>
</div>

<style type="text/css">
@import url(http://www.google.com/cse/api/overlay.css);
</style>

<script src="<?=$CONF["func"]["google"]["cse"]["keysrc"]?>" type="text/javascript"></script>
<script src="http://www.google.com/cse/api/overlay.js"></script>
<script type="text/javascript">
function OnLoad() {
  new CSEOverlay("<?=$CONF["func"]["google"]["cse"]["cseid"]?>",
                 document.getElementById("searchbox_<?=$CONF["func"]["google"]["cse"]["cseid"]?>"),
                 document.getElementById("results_<?=$CONF["func"]["google"]["cse"]["cseid"]?>"));
}
GSearch.setOnLoadCallback(OnLoad);
</script>
<!-- Google CSE Code ends -->

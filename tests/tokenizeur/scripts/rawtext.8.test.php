<?php

function show_install() {
	if(VIEW_OFF) return;
?>
<script type="text/javascript">
function showmessage(message) {
	document.getElementById('notice').innerHTML += message + '<br />';
	document.getElementById('notice').scrollTop = 100000000;
}
function initinput() {
	window.location='index.php?method=ext_info';
}
</script>
	<div class="main">
		<div class="btnbox"><div id="notice"></div></div>
		<div class="btnbox marginbot">
	<input type="button" name="submit" value="<?=lang('install_in_processed')?>" disabled style="height: 25" id="laststep" onclick="initinput()">
	</div>
<?php
}
?>
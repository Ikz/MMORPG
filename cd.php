<?php
include "globals.php"; ?>
<html>
<style type="text/css">
@import "css/jquery.countdown.css";

#defaultCountdown { width: 240px; height: 45px; }
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.countdown.js"></script>
<script type="text/javascript">
$(function () {
	var austDay = new Date();
	austDay = new Date(austDay.getFullYear() + 1, 7 - 1, 4);
	$('#defaultCountdown').countdown({until: austDay});
	$('#year').text(austDay.getFullYear());
});
</script>
<div id="defaultCountdown"></div>
</html>
<? $h->endpage(); ?>
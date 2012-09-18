<?php
session_start();
?>
<html>
<head>
	<title>nbar1</title>
	<link rel="stylesheet" href="/assets/stylesheets/global.css" />
	<?php
	$iPod = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
	$iPhone = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
	$iPad = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
	$Android= stripos($_SERVER['HTTP_USER_AGENT'],"Android");
	$webOS= stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
	if($iPod || $iPhone || $iPad)
	{
		echo "<link rel=\"stylesheet\" href=\"/assets/stylesheets/iDevice.css\" />";
	}
	?>
	<meta name="viewport" content="width=device-width; initial-scale=0.2; maximum-scale=0.2; user-scalable=no;"/>
</head>
<body>
<div id="search">
	<input type="text" class="searchbox" name="query" placeholder="search" />
	<div class="search_submit">GO</div>
	<div style="clear: both"></div>
</div>
<div id="content">
	<div id="content_loader"><div id="error_message_generic">loading</div></div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="/assets/javascripts/global.js"></script>
</script>
</body>
</html>

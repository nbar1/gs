<?php
session_start();
?>
<html>
<head>
	<title>gs</title>
	<link rel="stylesheet" href="assets/stylesheets/bootstrap.css" />
	<link rel="stylesheet" href="assets/stylesheets/global.css" />
	<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1; user-scalable=no;"/>
</head>
<body>
<div class="navbar navbar-fixed-top nav col-lg-12">
	<div class="input-group">
		<input type="text" id="search_input" name="query" class="form-control input-lg" placeholder="search">
		<span class="input-group-btn">
			<button id="search_submit" class="btn btn-default btn-lg" type="button">GO</button>
		</span>
	</div>
</div>
<div class="row-fluid">
	<div id="content">
		<div id="content_loader"><div id="error_message_generic">loading</div></div>
		<div id="modal"></div>
	</div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="assets/javascripts/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/javascripts/global.js"></script>
</script>
</body>
</html>

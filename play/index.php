<html>
<head>
	<script src="../assets/swfobject/swfobject.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<style>
body {
	background: #000;
	color: #bbb;
	font-family: "Century Gothic";
}
#groovesharkPlayer {
	left: -9999px;
	position: fixed;
	top: -9999px;
}
#currentSong {
	margin-top: 20%;
	text-align: center;
}
#currentSong .songTitle {
	font-size: 64px;
	margin: 0 auto;
	text-shadow: 2px 2px 1px #666;
}
#currentSong .songArtist {
	font-size: 36px;
	margin: 30px auto;
	text-shadow: 1px 1px 1px #666;
}
</style>
</head>
<body>
<div id="player"></div>


<div id="currentSong">
	<div class="songTitle"></div>
	<div class="songArtist"></div>
</div>

<script type="text/javascript" src="../assets/javascripts/player.js"></script>
</body>
</html>

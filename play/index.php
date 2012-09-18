<html>
<head>
	<script src="/assets/swfobject/swfobject.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
</head>
<body>
<div id="player"></div>
<script>
swfobject.embedSWF("http://grooveshark.com/APIPlayer.swf", "player", "300", "300", "9.0.0", "", {}, {allowScriptAccess: "always"}, {id:"groovesharkPlayer", name:"groovesharkPlayer"}, function(e) {
	var element = e.ref;
	if (element)
	{
		setTimeout(function() {
			window.player = element;
			window.player.setVolume(99);
		}, 1500);
	} else {
	// Couldn't load player
	// Play sad trombone
	}
});


function playSong(songID) {
	$.ajax({
		url: "/api/songGetter.php",
		type: "POST",
		data: {
			song: songID
		},
		success: function(response) {
			console.log(response);
			var responseData = $.parseJSON(response);
			console.log(responseData);
			window.player.playStreamKey(responseData.StreamKey, responseData.StreamServerHostname, responseData.StreamServerID);
		}
	});
}
</script>
</body>
</html>

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




<script>
swfobject.embedSWF("http://grooveshark.com/APIPlayer.swf", "player", "300", "300", "9.0.0", "", {}, {allowScriptAccess: "always"}, {id:"groovesharkPlayer", name:"groovesharkPlayer"}, function(e)
{
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


function playSong(songID)
{
	$.ajax({
		url: "../api/songGetter.php",
		type: "POST",
		data: {
			song: songID
		},
		success: function(response) {
			var responseData = $.parseJSON(response);
			console.log(responseData);
			window.player.playStreamKey(responseData.StreamKey, responseData.StreamServerHostname, responseData.StreamServerID);
			var streamKey = responseData.StreamKey;
			var streamServerID = responseData.StreamServerID;
			var streamTime = responseData.uSecs / 1000 - 0; // - seconds of lag, 0 seems to work fine
			getSongInfo(songID);
			var t = setTimeout("markSong30Seconds('"+streamKey+"', "+streamServerID+")", 45000);
			var t = setTimeout("nextSong()", streamTime);
		},
		error: function() {
			console.log("no songs to play, waiting 30 seconds");
			var t = setTimeout("nextSong()", 30000);
			$('#currentSong .songTitle').html("No Songs Queued");
			$('#currentSong .songArtist').html("");
		}
	});
}

function nextSong()
{
	$.ajax({
		url: "../f.php?f=getNextSong",
		type: "POST",
		data: {},
		success: function(response){
			playSong(response);
		}
	});
}

function getSongInfo(songID)
{
	console.log('fire get song info');
	$.ajax({
		url: "../f.php?f=getSongInfo",
		type: "POST",
		data: {
			song: songID
		},
		success: function(response){
			var songInfo = $.parseJSON(response);
			$('#currentSong .songTitle').html(songInfo.SongName);
			$('#currentSong .songArtist').html(songInfo.ArtistName);
			console.log(response);
		}
	});
}

function markSong30Seconds(streamKey, streamServerID)
{
	console.log('fire mark song 30 seconds');
	$.ajax({
		url: "../f.php?f=markSong30Seconds",
		type: "POST",
		data: {
			streamKey: streamKey,
			streamServerID: streamServerID
		},
		success: function(response){
			console.log(response);
		}
	});
}

var t = setTimeout("nextSong()", 2000);
</script>
</body>
</html>

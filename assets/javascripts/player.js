function playSong(songID) {
	if(songID == "") {
		var t = setTimeout("nextSong()", 30000);
		$('#currentSong .songTitle').html("No Songs Queued");
		$('#currentSong .songArtist').html("");
	} else {
		$.ajax({
			url: "/gs/player/stream/"+songID,
			type: "GET",
			success: function(response) {
				console.log(response);
				var responseData = $.parseJSON(response);
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
}

function nextSong() {
	$.ajax({
		url: "/gs/player/next",
		type: "GET",
		success: function(response){
			console.log("nextsong: "+response);
			playSong(response);
		}
	});
}

function getSongInfo(songID) {
	$.ajax({
		url: "/gs/song/"+songID,
		type: "GET",
		success: function(response){
			console.log(response);
			var songInfo = $.parseJSON(response);
			$('#currentSong .songTitle').html(songInfo.title);
			$('#currentSong .songArtist').html(songInfo.artist);
		}
	});
}

function markSong30Seconds(streamKey, streamServerID) {
	$.ajax({
		url: "/gs/player/stream/validate",
		type: "POST",
		data: {
			streamKey: streamKey,
			streamServerID: streamServerID
		}
	});
}

swfobject.embedSWF("http://grooveshark.com/APIPlayer.swf", "player", "300", "300", "9.0.0", "", {}, {allowScriptAccess: "always"}, {id:"groovesharkPlayer", name:"groovesharkPlayer"}, function(e) {
	var element = e.ref;
	if (element)
	{
		setTimeout(function() {
			window.player = element;
			window.player.setVolume(99);
		}, 1500);
	}
});

var t = setTimeout("nextSong()", 2000);
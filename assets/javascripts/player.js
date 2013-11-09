var player = player || {};

player = {
	/**
	 * Initialize
	 */
	init: function() {
		player.bind();
		player.setupPlayer();
		var t = setTimeout("player.getNextSong()", 1000);
	},

	/**
	 * Set up bindings
	 */
	bind: function() {
		// nothing here until player controls
	},

	/**
	 * Loads the flash component for the stream
	 */
	setupPlayer: function() {
		swfobject.embedSWF("http://grooveshark.com/APIPlayer.swf", "player", "300", "300", "9.0.0", "", {}, {allowScriptAccess: "always"}, {id:"groovesharkPlayer", name:"groovesharkPlayer"}, function(e) {
			var element = e.ref;
			if (element)
			{
				setTimeout(function() {
					window.gsplayer = element;
					window.gsplayer.setVolume(99);
				}, 1500);
			}
		});
	},

	/**
	 * Gets the info for the given song and updated UI
	 */
	getSongInfo: function(token) {
		$.ajax({
			url: "/song/"+token,
			type: "GET",
			success: function(response){
				console.log(response);
				var songInfo = $.parseJSON(response);
				$('#currentSong .songTitle').html(songInfo.title);
				$('#currentSong .songArtist').html(songInfo.artist);
				if(songInfo.image != null && songInfo.image != 'default.jpg') {
					$('body').css('background-image', 'url(http://images.gs-cdn.net/static/albums/500_'+songInfo.image+')');
				} else {
					$('body').css('background-image', '');
				}
			}
		});
	},

	/**
	 * Get token of next song
	 */
	getNextSong: function() {
		$.ajax({
			url: "/player/next",
			type: "GET",
			success: function(response){
				player.playSong(response);
			}
		});
	},

	/**
	 * Get token of next song
	 */
	getNextSong: function() {
		$.ajax({
			url: "/player/next",
			type: "GET",
			success: function(response){
				player.playSong(response);
			}
		});
	},

	/**
	 * Mark song at 30 seconds
	 */
	markSong30Seconds: function(streamKey, streamServerID) {
		$.ajax({
			url: "/player/stream/validate",
			type: "POST",
			data: {
				streamKey: streamKey,
				streamServerID: streamServerID
			}
		});
	},

	/**
	 * Play a given song
	 */
	playSong: function(token) {
		if(token == "") {
			var t = setTimeout("player.getNextSong()", 30000);
			$('#currentSong .songTitle').html("No Songs Queued");
			$('#currentSong .songArtist').html("");
		} else {
			$.ajax({
				url: "/player/stream/"+token,
				type: "GET",
				success: function(response) {
					var responseData = $.parseJSON(response);
					window.gsplayer.playStreamKey(responseData.StreamKey, responseData.StreamServerHostname, responseData.StreamServerID);
					var streamKey = responseData.StreamKey;
					var streamServerID = responseData.StreamServerID;
					var streamTime = responseData.uSecs / 1000 - 0; // - seconds of lag, 0 seems to work fine
					player.getSongInfo(token);
					//var t = setTimeout("player.markSong30Seconds('"+streamKey+"', "+streamServerID+")", 45000);
					var t = setTimeout("player.getNextSong()", streamTime);
				},
				error: function() {
					var t = setTimeout("player.getNextSong()", 30000);
					$('#currentSong .songTitle').html("No Songs Queued");
					$('#currentSong .songArtist').html("");
				}
			});
		}
	}
}

player.init();
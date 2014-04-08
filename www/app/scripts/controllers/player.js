'use strict';

/**
 * Unfortunately the GrooveShark Streaming API only
 * allows for callbacks on the window object, so
 * we have to catch them there and send them into
 * our Angular controller.
 */
window.songStatus = function(status) {
	if(status == 'failed') {
		window.songComplete();
	}
}
window.songComplete = function() {
	var PlayerCtrl = angular.element($("#content")).scope();
	PlayerCtrl.markSongComplete();
	PlayerCtrl.getNextSong();
}
window.songPosition = function(position) {
	var PlayerCtrl = angular.element($("#content")).scope();
	PlayerCtrl.setSongPosition(position);
}

/**
 * Player Controller
 *
 * Handles the streaming of songs to the player page
 */
angular.module('gsApp')
.controller('PlayerCtrl', function($scope, $rootScope, $timeout, PlayerModel) {
	/**
	 * Set template URL
	 */
	$scope.templateUrl = 'views/player.html';

	/**
	 * Get Song Info
	 *
	 * @param token
	 * @param callback
	 */
	$scope.getSongInfo = function(token, callback) {
		PlayerModel.getSongInfo(token).then(function(songInfo) {;
			$scope.song = {
				id: songInfo.id,
				token: songInfo.token,
				title: songInfo.title,
				artist: songInfo.artist,
				artist_id: songInfo.artist_id,
				image: songInfo.image
			}
			callback();
		});
	}

	/**
	 * Get Next Song
	 *
	 * Gets the next song and plays it
	 */
	$scope.getNextSong = function() {
		var nextSong = PlayerModel.getNextSong().then(function(song) {
			if(song.token != undefined) {
				$scope.getSongInfo(song.token, function() {
					$scope.playSong();
				});
			}
			else {
				console.log('getting next song failed. song:');
				console.log(song);
			}
		});
	}

	/**
	 * Mark Song Complete
	 */
	$scope.markSongComplete = function() {
		PlayerModel.markSongComplete($scope.song.id).then(function(response) {
			if(response.success == false) {
				console.log('Error marking song complete');
			}
		});
	}

	/**
	 * Play Song
	 *
	 * Gets the stream information and informs the
	 * flash player to start the stream.
	 */
	$scope.playSong = function() {
		var songStream = PlayerModel.getSongStream($scope.song.token).then(function(response) {
			// Play song via GS Flash player
			window.gsplayer.playStreamKey(response.StreamKey, response.StreamServerHostname, response.StreamServerID);
			// Store stream information in song object
			$scope.song.streamKey = response.StreamKey;
			$scope.song.streamServer = response.StreamServerID;
			// Set page background to album art
			document.body.style.backgroundImage="url('http://images.gs-cdn.net/static/albums/500_" + $scope.song.image + "')";
		});
	}

	/**
	 * Set Song Position
	 *
	 * @param object position
	 */
	$scope.setSongPosition = function(position) {
		var positionPercent = ((position.position / position.duration) * 100).toFixed(0);
		$('#player .progress-bar-inner').css('width', positionPercent + '%');
	}

	/**
	 * Build Flash Stream Player
	 *
	 * This is on a timeout because 'swfobject'
	 * acts funny and is unpredictable.
	 */
	$timeout(function() {
		swfobject.embedSWF("http://grooveshark.com/APIPlayer.swf", "streamPlayer", "1", "1", "9.0.0", "", {}, {allowScriptAccess: "always"}, {id:"groovesharkPlayer", name:"groovesharkPlayer"},
			function(e) {
				if(e.ref) {
					$timeout(function() {
						window.gsplayer = e.ref;
						window.gsplayer.setVolume(99);
						window.gsplayer.setStatusCallback('songStatus');
						window.gsplayer.setSongCompleteCallback('songComplete');
						window.gsplayer.setPositionCallback('songPosition');
					}, 1500);
				}
			}
		);
	}, 500);

	/**
	 * Play initital song
	 */
	$timeout(function() {
		$scope.getNextSong();
	}, 2000);
});

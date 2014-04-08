'use strict';

angular.module('gsApp')
.controller('PlayerCtrl', function ($scope, $rootScope, $timeout, PlayerModel) {
	$scope.templateUrl = 'views/player.html';

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

	$scope.getNextSong = function() {
		var nextSong = PlayerModel.getNextSong().then(function(song) {
			$scope.getSongInfo(song.token, function() {
				$scope.playSong();
			});
		});
	}

	$scope.playSong = function() {
		var songStream = PlayerModel.getSongStream($scope.song.token).then(function(response) {
			window.gsplayer.playStreamKey(response.StreamKey, response.StreamServerHostname, response.StreamServerID);
			$scope.song.streamKey = response.StreamKey;
			$scope.song.streamServer = response.StreamServerID;

			document.body.style.backgroundImage="url('http://images.gs-cdn.net/static/albums/500_" + $scope.song.image + "')";

			var streamTime = response.uSecs / 1000;
			$timeout(function() {
				$scope.getNextSong();
			}, streamTime)
		});
	}

	// Build flash stream player
	$timeout(function() {
		swfobject.embedSWF("http://grooveshark.com/APIPlayer.swf", "streamPlayer", "1", "1", "9.0.0", "", {}, {allowScriptAccess: "always"}, {id:"groovesharkPlayer", name:"groovesharkPlayer"},
			function(e) {
				if(e.ref) {
					$timeout(function() {
						window.gsplayer = e.ref;
						window.gsplayer.setVolume(99);
					}, 1500);
				}
			}
		);
	}, 500);

	// Play songs
	$timeout(function() {
		$scope.getNextSong();
	}, 2000);
});

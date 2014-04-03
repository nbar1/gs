'use strict';

angular.module('gsApp')
.directive('songItem', function ($timeout) {
	return {
		restrict: 'A',
		templateUrl: 'views/d_song.html',
		link: function(scope, element, attr) {
			scope.status = 'waiting';
			scope.addSong = function(song, type) {
				scope.showLoading = true;
				scope.status = 'loading';
				scope.addSongToQueue(song, type, function(success) {
					scope.status = (success) ? 'success' : 'error';
					$timeout(function() {
						scope.showLoading = false;
						$timeout(function() {
							scope.status = 'waiting';
						}, 1000);
					}, 4000);
				});
			}
		}
	};
});
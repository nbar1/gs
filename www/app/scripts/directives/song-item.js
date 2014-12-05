'use strict';

/**
 * Song Item Directive
 *
 * Handles the display of an individual
 * song in a list.
 */
angular.module('gsApp')
.directive('songItem', function ($timeout) {
	return {
		restrict: 'A',
		templateUrl: 'views/d_song.html',
		link: function(scope, element, attr) {
			/**
			 * Status
			 */
			scope.status = {
				promote: 'waiting',
				add: 'waiting'
			}

			/**
			 * Add Song
			 *
			 * @param song
			 * @param type
			 */
			scope.addSong = function(song, promote) {
				if(promote) {
					scope.status.promote = 'loading';
				}
				else {
					scope.status.add = 'loading';
				}
				// Add song to queue
				scope.addSongToQueue(song, promote, function(success) {
					if(promote) {
						scope.status.promote = (success) ? 'success' : 'error';
					}
					else {
						scope.status.add = (success) ? 'success' : 'error';
					}
				});
			}
		}
	};
});
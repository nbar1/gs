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
			scope.status = 'waiting';

			/**
			 * Add Song
			 *
			 * @param song
			 * @param type
			 */
			scope.addSong = function(song, type) {
				// Show loading indicator
				scope.showLoading = true;
				// Set status as loading
				scope.status = 'loading';

				// Add song to queue
				scope.addSongToQueue(song, type, function(success) {
					// Set status
					scope.showLoading = false;
					scope.status = (success) ? 'success' : 'error';
				});
			}
		}
	};
});
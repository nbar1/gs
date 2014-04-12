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
					scope.status = (success) ? 'success' : 'error';
					// Set timeout to dismiss loading indicator
					$timeout(function() {
						// Hide loading indicator
						scope.showLoading = false;
						// Set timeout to return status to waiting
						$timeout(function() {
							scope.status = 'waiting';
						}, 1000);
					}, 4000);
				});
			}
		}
	};
});
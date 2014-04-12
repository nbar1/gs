'use strict';

/**
 * Player Model
 *
 * Handles http requests for player page
 */
angular.module('gsApp')
.factory('PlayerModel', function($http) {
	var SearchModel = {
		/**
		 * Get Next Song
		 *
		 * @return promise
		 */
		getNextSong: function() {
			var promise = $http({
				url: '/api/v1/player/next',
				method: 'GET',
			})
			.then(function(response) {
				return response.data;
			});
			return promise;
		},

		/**
		 * Get Song Stream
		 *
		 * @param token
		 * @return promise
		 */
		getSongStream: function(token) {
			var promise = $http({
				url: '/api/v1/player/stream/' + token,
				method: 'GET',
			})
			.then(function(response) {
				return response.data;
			});
			return promise;
		},

		/**
		 * Get Song Info
		 *
		 * @param token
		 * @return promise
		 */
		getSongInfo: function(token) {
			var promise = $http({
				url: '/api/v1/song/' + token,
				method: 'GET',
			})
			.then(function(response) {
				return response.data;
			});
			return promise;
		},

		/**
		 * Mark Song Complete
		 *
		 * @param song_id
		 * @return promise
		 */
		markSongComplete: function(song_id) {
			var promise = $http({
				url: '/api/v1/song/' + song_id + '/played',
				method: 'POST',
			})
			.then(function(response) {
				return response.data;
			});
			return promise;
		}
	};
	return SearchModel;
});
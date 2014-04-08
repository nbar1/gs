'use strict';

angular.module('gsApp')
.factory('PlayerModel', function($http) {
	var SearchModel = {
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
'use strict';

angular.module('gsApp')
.factory('SearchModel', function($cookies, $http) {
	var SearchModel = {
		doSearch: function(query, type) {
			var url = (type == 'artist') ? '/api/v1/search/artist/' : '/api/v1/search/';
			url = url + query + "?apikey=" + $cookies.gs_apikey;
			var promise = $http({
				url: url,
				method: 'GET',
			})
			.then(function(response) {
				return response.data;
			});
			return promise;
		},

		addSongToQueue: function(song, promote) {
			var priority = (promote) ? 'high' : 'low';
			var post = {
				songID: song.SongID,
				songPriority: priority
			}
			var promise = $http({
				url: '/api/v1/queue/add/?apikey=' + $cookies.gs_apikey,
				method: 'POST',
				data: post,
			})
			.then(function(response) {
				return response.data;
			});
			return promise;
		}
	};
	return SearchModel;
});
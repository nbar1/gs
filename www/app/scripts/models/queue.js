'use strict';

angular.module('gsApp')
.factory('QueueModel', function($http, $cookies) {
	var QueueModel = {
		getQueue: function() {
			var promise = $http({
				url: '/api/v1/queue/?apikey=' + $cookies.gs_apikey,
				method: 'GET',
			})
			.then(function(response) {
				return response.data;
			});
			return promise;
		}
	};
	return QueueModel;
});
'use strict';

/**
 * Queue Model
 *
 * Handles http requests for queue interaction
 */
angular.module('gsApp')
.factory('QueueModel', function($rootScope, $http, $cookies, $location) {
	var QueueModel = {
		/**
		 * Get Queue
		 *
		 * @return promise
		 */
		getQueue: function() {
			var promise = $http({
				url: '/api/v1/queue/?apikey=' + $rootScope.apikey,
				method: 'GET',
			})
			.success(function(response, status) {
				return response;
			})
			.error(function(response, status) {
				$cookies.gs_apikey = undefined;
				$rootScope.apikey = undefined;
				$location.path('/login');
			});
			return promise;
		}
	};
	return QueueModel;
});
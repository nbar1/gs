'use strict';

angular.module('gsApp')
.controller('QueueCtrl', function ($scope, $rootScope, $cookies, $location, $http) {
	$rootScope.searchbox = true;
	$rootScope.showQueueButton = false;
	
	// Check if user has apikey
	if(!$cookies.gs_apikey) {
		$location.path('/');
	}

	$http({
		url: '/api/v1/queue?apikey=' + $cookies.gs_apikey,
		method: 'GET',
	})
	.success(function(data) {
		$scope.templateUrl = 'views/queue.html';
		$scope.queue = data.queue;
	});
});

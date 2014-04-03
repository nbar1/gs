'use strict';

angular.module('gsApp')
.controller('QueueCtrl', function ($scope, $rootScope, $cookies, $location, $interval, QueueModel) {
	$rootScope.searchbox = true;
	$rootScope.showQueueButton = false;
	
	// Check if user has apikey
	if(!$cookies.gs_apikey) {
		$location.path('/');
	}
	else {
		$scope.getQueue = function() {
			QueueModel.getQueue().then(function(response) {
				$scope.queue = response.queue;
				$scope.templateUrl = 'views/queue.html';
			});
		}
		$scope.fetchQueue = $interval(function() {
			$scope.getQueue()
		}, 10000);
		$scope.getQueue();
	}
});

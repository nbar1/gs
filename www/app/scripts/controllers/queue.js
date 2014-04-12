'use strict';

/**
 * Queue Controller Controller
 *
 * Handles display of the song queue
 */
angular.module('gsApp')
.controller('QueueCtrl', function ($scope, $rootScope, $location, $interval, QueueModel) {
	/**
	 * Get Queue
	 */
	$scope.getQueue = function() {
		QueueModel.getQueue().success(function(response, status) {
			$scope.queue = response.queue;
			$scope.templateUrl = 'views/queue.html';
		});
	}

	/**
	 * Destroy
	 *
	 * Cancel queue promise
	 */
	$scope.$on('$destroy', function(){
		$interval.cancel($scope.queuePromise);
	});

	/**
	 * Check if user has apikey
	 */
	if(!$rootScope.apikey) {
		$location.path('/login');
	}
	else {
		$scope.queuePromise = $interval(function() { $scope.getQueue() }, 10000);
		$scope.getQueue();
	}
});

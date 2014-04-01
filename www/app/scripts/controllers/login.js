'use strict';

angular.module('gsApp')
.controller('LoginCtrl', function ($scope, $rootScope, $cookies, $location, $http) {
	$scope.searchbox = false;
	
	// If user already has an apikey, send them to the queue
	if($cookies.gs_apikey) {
		$location.path('/queue');
	}

	// Submit a login
	$scope.login = function(isValid) {
		if(isValid) {
			$http({
				url: '/api/v1/login',
				method: 'POST',
				data: {username: $scope.username, password: $scope.password}
			})
			.success(function(data) {
				if(data.success) {
					$cookies.gs_apikey = data.api_key;
					$rootScope.username = data.username;
					$rootScope.promotions = data.promotions;
					$location.path('/queue');
				}
				else {
					$scope.error_message = data.message;
				}
			});
		}
	}
});

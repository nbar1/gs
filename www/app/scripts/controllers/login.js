'use strict';

angular.module('gsApp')
.controller('LoginCtrl', function ($scope, $rootScope, $cookies, $location, LoginModel) {
	$rootScope.searchbox = false;
	
	// If user already has an apikey, send them to the queue
	if($cookies.gs_apikey) {
		$location.path('/queue');
	}

	// Submit a login
	$scope.login = function(isValid) {
		if(isValid) {
			LoginModel.login($scope.username, $scope.password).then(function(response) {
				if(response.success) {
					$cookies.gs_apikey = response.api_key;
					$location.path('/queue');
				}
				else {
					$scope.error_message = response.message;
				}
			});
		}
	}
});

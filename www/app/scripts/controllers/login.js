'use strict';

/**
 * Login Controller
 *
 * Handles user authentication
 */
angular.module('gsApp')
.controller('LoginCtrl', function ($scope, $rootScope, $cookies, $location, LoginModel) {
	/**
	 * Login
	 *
	 * @param isValid AngularJS validation result
	 */
	$scope.login = function(isValid) {
		if(isValid) {
			LoginModel.login($scope.username, $scope.password).then(function(response) {
				if(response.success) {
					$cookies.gs_apikey = response.api_key;
					$rootScope.apikey = response.api_key;
					$location.path('/');
				}
				else {
					$scope.error_message = response.message;
				}
			});
		}
	}
});

'use strict';

/**
 * Login Model
 *
 * Handles http requests for user authentication
 */
angular.module('gsApp')
.factory('LoginModel', function($http) {
	var LoginModel = {
		/**
		 * Login
		 *
		 * @param username
		 * @param password
		 * @return promise
		 */
		login: function(username, password) {
			var promise = $http({
				url: '/api/v1/login',
				method: 'POST',
				data: {username: username, password: password}
			})
			.then(function(response) {
				return response.data;
			});
			return promise;
		}
	};
	return LoginModel;
});
'use strict';

angular.module('gsApp')
.factory('LoginModel', function($http) {
	var QueueModel = {
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
	return QueueModel;
});
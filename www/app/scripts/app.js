'use strict';

angular.module('gsApp', [
	'ngCookies',
	'ngResource',
	'ngSanitize',
	'ngRoute',
	'ngTouch',
])
.config(function($routeProvider) {
	$routeProvider
		.when('/', {
			templateUrl: 'views/login.html',
			controller: 'LoginCtrl'
		})
		.when('/queue', {
			template: '<div ng-hide="queue || errorMessage" loader></div><div ng-include="templateUrl"></div>',
			controller: 'QueueCtrl'
		})
		.when('/search/:query/:type?', {
			template: '<div ng-hide="songs || errorMessage" loader></div><div ng-include="templateUrl"></div>',
			controller: 'SearchCtrl'
		})
		.otherwise({
			redirectTo: '/'
		});
});


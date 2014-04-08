'use strict';

angular.module('gsApp', [
	'ngCookies',
	'ngResource',
	'ngRoute',
	'ngTouch',
])
.run(function($rootScope, $templateCache, $cookies) {
	$rootScope.$on('$viewContentLoaded', function() {
		$templateCache.removeAll();
	});
	$rootScope.$on("$routeChangeSuccess", function(e, route) {
		// Set up SearchBox
		$rootScope.searchBox = {
			active: false,
			showQueueButton: false
		};
		if(route.$$route !== undefined && route.$$route.searchBox !== undefined) {
			$rootScope.searchBox.active = route.$$route.searchBox.active;
			$rootScope.searchBox.showQueueButton = route.$$route.searchBox.showQueueButton;
		}
		
		// Check for API Key in cookie
		if($cookies.gs_apikey !== undefined) {
			$rootScope.apikey = $cookies.gs_apikey;
		}
	});
})
.config(function($routeProvider) {
	$routeProvider
		.when('/', {
			templateUrl: 'views/login.html',
			controller: 'LoginCtrl'
		})
		.when('/queue', {
			template: '<div ng-hide="queue || errorMessage" loader></div><div ng-include="templateUrl"></div>',
			controller: 'QueueCtrl',
			searchBox: {
				active: true,
				showQueueButton: false
			}
		})
		.when('/search/:query/:type?', {
			template: '<div ng-hide="songs || errorMessage" loader></div><div ng-include="templateUrl"></div>',
			controller: 'SearchCtrl',
			searchBox: {
				active: true,
				showQueueButton: true
			}
		})
		.when('/player', {
			template: '<div ng-hide="song || errorMessage" loader></div><div ng-include="templateUrl"></div>',
			controller: 'PlayerCtrl'
		})
		.otherwise({
			redirectTo: '/'
		});
});

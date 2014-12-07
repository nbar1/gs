'use strict';

angular.module('gsApp', [
	'ngCookies',
	'ngResource',
	'ngRoute',
	'ngTouch',
	'ui.slider',
])
.run(function($rootScope, $templateCache, $cookies) {
	/**
	 * View Content Loaded
	 */
	$rootScope.$on('$viewContentLoaded', function() {
		$templateCache.removeAll();
	});
	/**
	 * Route Change Success
	 */
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
		if($cookies.gs_apikey != undefined) {
			$rootScope.apikey = $cookies.gs_apikey;
		}
	});
})
.config(function($routeProvider) {
	/**
	 * Routes
	 */
	$routeProvider
		/**
		 * Queue page
		 */
		.when('/', {
			template: '<div ng-hide="queueLoaded || errorMessage" loader></div><div ng-include="templateUrl"></div>',
			controller: 'QueueCtrl',
			searchBox: {
				active: true,
				showQueueButton: false
			}
		})
		/**
		 * Login page
		 */
		.when('/login', {
			templateUrl: 'views/login.html',
			controller: 'LoginCtrl'
		})
		/**
		 * Search page
		 *
		 * @param query
		 * @param type [optional]
		 */
		.when('/search/:query/:type?', {
			template: '<div ng-hide="songs || errorMessage" loader></div><div ng-include="templateUrl"></div>',
			controller: 'SearchCtrl',
			searchBox: {
				active: true,
				showQueueButton: true
			}
		})
		/**
		 * Player page
		 */
		.when('/player', {
			template: '<div ng-hide="song || errorMessage" loader></div><div ng-include="templateUrl"></div>',
			controller: 'PlayerCtrl'
		})
		/**
		 * Redirect to queue
		 */
		.otherwise({
			redirectTo: '/'
		});
});

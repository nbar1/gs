'use strict';

/**
 * Search Box Controller
 *
 * Handles the display of the search box
 */
angular.module('gsApp')
.controller('SearchBoxCtrl', function ($scope, $location) {
	$scope.doSearch = function(query) {
		if(query) {
			$location.path('/search/' + query);
		}
	};

	$scope.gotoQueue = function() {
		$location.path('/');
	}
});

'use strict';

angular.module('gsApp')
.controller('SearchBoxCtrl', function ($scope, $rootScope, $location, SearchService) {
	$scope.doSearch = function(query) {
		if(query) {
			$location.path('/search/' + query);
		}
	};
	
	$scope.gotoQueue = function() {
		$location.path('/queue');
	}
});

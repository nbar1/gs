'use strict';

angular.module('gsApp')
.controller('SearchBoxCtrl', function ($scope, $rootScope, $location) {
	$scope.doSearch = function(query) {
		if(query) {
			$location.path('/search/' + query);
		}
	};
	
	$scope.gotoQueue = function() {
		$location.path('/queue');
	}
});

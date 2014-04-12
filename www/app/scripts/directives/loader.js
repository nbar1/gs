'use strict';

/**
 * Loader Directive
 *
 * Handles the loading indicator
 */
angular.module('gsApp')
.directive('loader', function () {
	return {
		restrict: 'A',
		templateUrl: 'views/d_loader.html',
	};
});
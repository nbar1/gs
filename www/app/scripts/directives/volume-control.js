'use strict';

/**
 * Song Item Directive
 *
 * Handles the display of volume contrl
 */
angular.module('gsApp')
.directive('volumeControl', function ($timeout) {
	return {
		restrict: 'A',
		templateUrl: 'views/d_volume_control.html'
	};
});
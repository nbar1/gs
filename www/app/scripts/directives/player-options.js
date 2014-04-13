'use strict';

/**
 * Player Options Directive
 *
 * Handles the display of an player options panel
 */
angular.module('gsApp')
.directive('playerOptions', function ($timeout) {
	return {
		restrict: 'A',
		templateUrl: 'views/d_player_options.html'
	};
});
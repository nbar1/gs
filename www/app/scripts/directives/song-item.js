'use strict';

angular.module('gsApp')
.directive('songItem', function () {
	return {
		restrict: 'A',
		templateUrl: 'views/d_song.html',
	};
});
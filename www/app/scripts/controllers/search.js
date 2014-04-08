'use strict';

angular.module('gsApp')
.controller('SearchCtrl', function ($scope, $rootScope, $cookies, $location, $routeParams, SearchModel) {
	$scope.searchType = ($routeParams.type) ? $routeParams.type : 'full';
	$scope.searchQuery = $routeParams.query;

	$scope.search = function() {
		console.log('searching for ' + $scope.searchQuery);
		SearchModel.doSearch($scope.searchQuery, $scope.searchType)
			.then(function(data) {
				if(data.songs.length < 1) {
					data.success = false;
				}
				if(data.success) {
					$scope.templateUrl = (data.type == 'full') ? 'views/search_full.html' : 'views/search_songs.html';
					if(data.artists) {
						$scope.artists = data.artists;
					}
					$scope.songs = data.songs;
					return true;
				}
				else {
					$scope.errorMessage = "No Results Found";
					$scope.templateUrl = "views/error.html";
					return false;
				}
			});
	}
	
	$scope.artistSearch = function(artist) {
		if(artist.ArtistID !== undefined) {
			$location.path('search/' + artist.ArtistID + '/artist');
		}
	}
	
	$scope.addSongToQueue = function(song, promote, callback) {
		SearchModel.addSongToQueue(song, promote)
			.then(function(data) {
				if(data.success) {
					callback(true);
				}
				else {
					callback(false);
				}
			});
	}

	if(!$rootScope.apikey || $scope.searchQuery == undefined) {
		$location.path('/');
	}
	else {
		$scope.search();
	}
});

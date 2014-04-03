'use strict';

angular.module('gsApp')
.controller('SearchCtrl', function ($scope, $rootScope, $cookies, $location, $routeParams, SearchModel) {
	$rootScope.searchbox = true;
	$rootScope.showQueueButton = true;
	$scope.showOptions = false;
	$scope.songLoading = false;
	
	$scope.searchType = ($routeParams.type) ? $routeParams.type : 'full';
	$scope.searchQuery = $routeParams.query;

	if(!$cookies.gs_apikey) {
		$location.path('/');
	}

	$scope.search = function() {
		if($scope.searchQuery == undefined) {
			$location.path('/');
		}
		else {
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
					}
					else {
						$scope.errorMessage = "No Results Found";
						$scope.templateUrl = "views/error.html";
					}
				});
		}
	}
	
	$scope.artistSearch = function(artist) {
		$location.path('search/' + artist.ArtistID + '/artist');
	}
	
	$scope.addSongToQueue = function(song, promote, callback) {
		SearchModel.addSongToQueue(song, promote)
			.then(function(data) {
				if(data.success) {
					console.log('returning true!');
					callback(true);
				}
				else {
					callback(false);
				}
			});
	}

	$scope.search();
});

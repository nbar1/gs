'use strict';

/**
 * Search Controller
 *
 * Handles the searching of a query
 */
angular.module('gsApp')
.controller('SearchCtrl', function ($scope, $rootScope, $cookies, $location, $routeParams, SearchModel) {
	/**
	 * Search Type
	 */
	$scope.searchType = ($routeParams.type) ? $routeParams.type : 'full';

	/**
	 * Search Query
	 */
	$scope.searchQuery = $routeParams.query;

	/**
	 * Search
	 *
	 * Searches for the defined query
	 *
	 * @return bool
	 */
	$scope.search = function() {
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
					$scope.userPromotions = data.userPromotions;
					return true;
				}
				else {
					$scope.errorMessage = "No Results Found";
					$scope.templateUrl = "views/error.html";
					return false;
				}
			});
	}

	/**
	 * Artist Search
	 * 
	 * @param artist
	 */
	$scope.artistSearch = function(artist) {
		if(artist.ArtistID !== undefined) {
			$location.path('search/' + artist.ArtistID + '/artist');
		}
	}

	/**
	 * Add Song To Queue
	 *
	 * @param song
	 * @param promote
	 * @param callback
	 */
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

	/**
	 * Check for API key
	 */
	if(!$rootScope.apikey || $scope.searchQuery == undefined) {
		$location.path('/');
	}
	else {
		$scope.search();
	}
});

'use strict';

angular.module('gsApp')
.controller('SearchCtrl', function ($scope, $rootScope, $cookies, $location, $http, $routeParams) {
	$rootScope.searchbox = true;
	$rootScope.showQueueButton = true;
	$scope.showOptions = false;
	
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
			var url = ($scope.searchType == 'artist') ? '/api/v1/search/artist/' : '/api/v1/search/';
			url = url + $scope.searchQuery + "?apikey=" + $cookies.gs_apikey;
			$http({
				url: url,
				method: 'GET',
			})
			.success(function(data) {
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
					$scope.errorMessage = "No Songs Found";
					$scope.templateUrl = "views/error.html";
				}
			});
		}
	}
	
	$scope.artistSearch = function(artist) {
		$location.path('search/' + artist.ArtistID + '/artist');
	}
	
	$scope.addSongToQueue = function(song, promote) {
		var priority = (promote) ? 'high' : 'low';
		var post = {
			songID: song.SongID,
			songPriority: priority
		}
		$http({
			url: '/api/v1/queue/add/?apikey=' + $cookies.gs_apikey,
			method: 'POST',
			data: post,
		})
		.success(function(data) {
			if(data.success) {
				
			}
			else {
				$scope.error_message = data.message;
			}
		});
	}

	$scope.search();
});

describe("gsApp", function() {
	beforeEach(function() {
		module('gsApp');
	});

	describe("SearchCtrl", function() {
		beforeEach(inject(function($injector) {
			$location = $injector.get('$location');
			$rootScope = $injector.get('$rootScope');
			$rootScope.apikey = "valid_api_key";
			$scope = $rootScope.$new();

			$controller = $injector.get('$controller');

			createController = function() {
				return $controller('SearchCtrl', {
					'$scope': $scope
				});
			};
		}));
		
		/**
		 * Test Artist Search with valid artist
		 */
		it("Should go to artist search page for artist with an ID of 17", function() {
			var controller = createController();
			$scope.artistSearch({ArtistID: 17});
			expect($location.path()).toBe("/search/17/artist");
		});
		
		/**
		 * Test Artist Search with invalid artist
		 */
		it("Should not perform an artist search if the ID is undefined", function() {
			var controller = createController();
			$location.path('/search');
			$scope.artistSearch({ArtistID: undefined});
			expect($location.path()).toBe("/search");
		});
		
		/**
		 * Test search type set to full when not given
		 */
		it("should set searchType to 'full' when none given", function() {
			var controller = createController();
			$location.path('/search');
			expect($scope.searchType).toBe("full");
		});
		
		/**
		 * Test search type set to artist when given 'artist'
		 */
		it("should set searchType to 'artist' when given 'artist'", function() {
			var controller = $controller('SearchCtrl', {
				'$scope': $scope,
				'$routeParams': {
					type: 'artist'
				}
			});
			expect($scope.searchType).toBe("artist");
		});
		
		/**
		 * Test search for given query
		 */
		it("should search for the given query", function() {
			var controller = $controller('SearchCtrl', {
				'$scope': $scope,
				'$routeParams': {
					query: 'Some Random Song'
				}
			});
			expect($scope.searchQuery).toBe("Some Random Song");
			console.log($scope);
			var songs = SearchModel.doSearch($scope.searchQuery).then(function(data) {
				
			});
			expect($scope.search()).toBe(true);
		});
		
		/**
		 * Test search for given query via route
		 */
		it("should search for the given query", function() {
			var controller = $controller('SearchCtrl', {
				'$scope': $scope,
				'$routeParams': {
					query: 'Some Random Song'
				}
			});
		});
	});
});
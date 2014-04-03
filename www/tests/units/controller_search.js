describe("gsApp", function() {
	beforeEach(function() {
		module('gsApp');
	});

	describe("SearchCtrl", function() {
		var scope;
		beforeEach(inject(function($rootScope, $controller, $location) {
			scope = $rootScope.$new();
			$controller("SearchCtrl", {
				$scope: scope
			});
		}));

		it("should go to artist search page and search for Brand New", function() {
			scope.artistSearch("Brand New");
			expect($location.path()).toBe("search/Brand%20New/artist");
		});
	});
});
'use strict';

var moviesNolteModule = angular.module('moviesNolte', []);

moviesNolteModule.factory('MoviesService', function( $http ){
    var MoviesService = {
        getAll: function() {
            
            var promise = $http({method: 'GET', url: 'http://localhost/wordpress/movies.json' }).success( function( response) {
                return response.data;
            });
            
            return promise;
            
        }
    };
    
    return MoviesService;
});

moviesNolteModule.directive('moviesNolteList', function( MoviesService ) {
    return {
        restrict: 'A',
        scope: {
            'moviesNolteList': '@'
        },
        templateUrl: 'wp-content/plugins/nolte-test/js/movies-list.html',
        link: function($scope, element, attrs) {     
            $scope.moviesData = eval('(' + $scope.moviesNolteList + ')');
            console.log($scope.moviesData);
        }
    };
})
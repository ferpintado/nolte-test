'use strict';

var moviesNolteModule = angular.module('moviesNolte', []);

moviesNolteModule.factory('MoviesService', function( $http ){
    var MoviesService = {
        getAll: function() {
            
            var promise = $http.get( 'movies.json' ).then( function( response) {
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
        templateUrl: '/wp-content/plugins/moviesnolte/js/movies-list.html',
        link: function() {
            MoviesService.getAll().then( function( data ){
                console.log( data );
            })
            
        }
    };
})
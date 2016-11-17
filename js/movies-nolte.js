'use strict';

var moviesNolteModule = angular.module('moviesNolte', []);

moviesNolteModule.directive('moviesNolteList', function( $sce ) {
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
var mainApp = angular.module('mainApp', [
    'ngRoute',
    'ngResource',
    'mainModule',
    'ui.bootstrap.datetimepicker'
]);

mainApp.config(['$routeProvider', '$compileProvider',
    function($routeProvider, $compileProvider) {
        $routeProvider.
            when('/register', {
                templateUrl: '/partials/register.html',
                controller: 'UserController'
            }).
            when('/login', {
                templateUrl: '/partials/login.html',
                controller: 'LoginController'
            }).
            when('/rows', {
                templateUrl: '/partials/rows.html',
                controller: 'RowsController'
            }).
            when('/rows/user/:userId', {
                templateUrl: '/partials/rows.html',
                controller: 'RowsController'
            }).
            when('/settings', {
                templateUrl: '/partials/settings.html',
                controller: 'SettingsController'
            }).
            when('/users', {
                templateUrl: '/partials/users.html',
                controller: 'UsersController'
            }).
            when('/logout', {
                templateUrl: '/partials/blank.html',
                controller: 'LogoutController'
            }).
            otherwise({
                redirectTo: '/login'
            });

        $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|file|blob):/);
    }]);

mainApp.run(function($rootScope, $http) {

    $http.defaults.headers.common['X-Auth-Token'] = $.cookie('token');
    $rootScope.userId = $.cookie('userid');
    $rootScope.role = $.cookie('role');
    $rootScope.hours = $.cookie('hours');

});


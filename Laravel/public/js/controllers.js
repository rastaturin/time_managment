var mainModule = angular.module('mainModule', []);

mainApp.factory('User', function($resource) {
    var User = $resource('/api/user/:id', {id: '@id'}, {
        login: {
            url: 'api/user/login',
            method: 'POST'
        },
        update: {
            method: 'PUT'
        }
    });

    User.prototype.role = 0;

    return User;
});

mainApp.factory('TimeRow', function($resource) {
    var TimeRow = $resource('/api/user/:userId/timerow/:rowId', {userId: '@userId', rowId: '@id'},
        {
            'update': {method: 'PUT'}
        });

    TimeRow.index = function(userId) {
        return this.query({userId: userId}, null, function(data) {
            if (data.status == 401) {
                $location.path("/login");
            }
        });
    };

    return TimeRow;
});


mainModule.controller('LoginController', function ($scope, $location, User, $rootScope, $http) {
    $scope.user = new User();
    $scope.login = function() {
        $scope.user.$login(function(data){
            if (data.token) {
                $rootScope.userId = data.user.id;
                $rootScope.role = data.user.role;
                $rootScope.hours = data.user.hours;
                $.cookie('token', data.token);
                $.cookie('userid', data.user.id);
                $.cookie('role', data.user.role);
                $.cookie('hours', data.user.hours);
                $http.defaults.headers.common['X-Auth-Token'] = $.cookie('token');
                $location.path("/rows");
            } else {
                $("#inputPassword").parent().addClass('has-error');
            }
        });
    };
});

mainModule.controller('RowsController', function ($scope, $location, User, TimeRow, $rootScope, $routeParams, $http, $window) {

    $scope.userId = $routeParams.userId ? $routeParams.userId : $rootScope.userId;

    $scope.timerow = new TimeRow();
    $scope.timerow.date = moment().format("YYYY-MM-DD");
    $scope.timerow.note = "New work";

    $scope.dateFrom = null;
    $scope.dateTo = null;

    $scope.timerows = TimeRow.index($scope.userId);

    $scope.save = function() {
        if ($scope.timerow.id) {
            TimeRow.update({rowId:$scope.timerow.id, userId : $scope.userId}, $scope.timerow, function () {
                $scope.timerows = TimeRow.index($scope.userId);
            });
        } else {
            $scope.timerow.$save({userId : $scope.userId}, function () {
                $scope.timerows = TimeRow.index($scope.userId);
            });
        }
        $("#workForm").slideUp();
    };

    $scope.delete = function(curTimerow) {
        curTimerow.$delete({userId : $scope.userId, rowId: curTimerow.id});
        var index = $scope.timerows.indexOf(curTimerow);
        $scope.timerows.splice(index, 1);
    };

    $scope.set = function(curTimerow) {
        $scope.timerow = curTimerow;
        $("#workForm").slideDown();
    };

    $scope.showDate = function(date) {
        if (date) {
            return moment(date).format("L");
        }
    };

    $scope.format = function(str) {
        if (str) {
            return str.toLocaleFormat("%Y-%m-%d");
        } else {
            return '0';
        }
    };

    $scope.downloads = [];

    $scope.export = function() {
        var url = '/api/user/' + $scope.userId +'/timerow/export/' + $scope.format($scope.dateFrom) + '/' + $scope.format($scope.dateTo);
        $http.get(url).success(function(data) {

            var blob = new Blob([data], { type: 'application/octet-stream' });
            var url = URL.createObjectURL(blob);
            var date = "All dates";
            if ($scope.dateFrom) {
                if ($scope.dateTo) {
                    date = $scope.showDate($scope.dateFrom) + '-' + $scope.showDate($scope.dateTo);
                } else {
                    date = 'From ' + $scope.showDate($scope.dateFrom);
                }
            } else if ($scope.dateTo) {
                date = 'To ' + $scope.showDate($scope.dateTo);
            }

            var download = {href: url, date: date, filename: date + '.html'};
            $scope.downloads.push(download);
            window.open(url);
        });
    };

    $scope.reset = function() {
        $scope.dateFrom = null;
        $scope.dateTo = null;
    };

    $scope.addNew = function() {
        $scope.timerow = new TimeRow();
        $scope.timerow.date = moment().format("YYYY-MM-DD");
        $scope.timerow.note = "New work";
        $("#workForm").slideDown();
    }

});

mainModule.controller('UsersController', function ($scope, $location, User, $rootScope) {

    $scope.user = new User();
    $scope.users = User.query({}, null, function(data) {
        if (data.status == 401) {
            $location.path("/login");
        }
    });

    $scope.save = function() {
        if ($scope.user.id) {
            User.update({id : $scope.user.id}, $scope.user, function () {
                $scope.users = User.query();
            });
        } else {
            $scope.user.$save(function () {
                $scope.users = User.query();
            });
        }
        $("#userForm").slideUp();
    };

    $scope.delete = function(user) {
        user.$delete({id : user.id});
        var index = $scope.users.indexOf(user);
        $scope.users.splice(index, 1);
    };

    $scope.set = function(user) {
        $scope.user = user;
        $("#userForm").slideDown();
    };

    $scope.addNew = function() {
        $scope.user = new User();
        $("#userForm").slideDown();
    }

    $scope.getRole = function(role) {
        return role == 2 ? 'Admin' : role == 1 ? 'Manager' : 'User';
    }

});

mainModule.controller('UserController', function ($scope, User) {

    $scope.user = new User();
    $scope.register = function() {
        $scope.user.$save(function(data) {
                $("#registerForm").slideUp();
                $("#thanks").slideDown();
        });
    };
});

mainModule.controller('LogoutController', function ($location, $rootScope) {

    $.cookie('token', '');
    $.cookie('userid', '');
    $.cookie('role', '');
    $.cookie('hours', '');
    $rootScope.userId = null;
    $rootScope.role = null;
    $rootScope.hours = null;

    $location.path("/login");

});

mainModule.controller('SettingsController', function ($scope, User, $rootScope, $timeout) {

    $scope.user = new User.get({id: $.cookie('userid')}, function(data){
        if ($scope.user.hours == 0) {
            $scope.user.hours = null;
        }
    });

    $scope.save = function() {
        User.update({ id: $rootScope.userId }, $scope.user, function(data) {
            $rootScope.infoMessage = "Saved";
            $rootScope.hours = $scope.user.hours;
            $.cookie('hours', $rootScope.hours);
            $timeout(function(){$rootScope.infoMessage = ""}, 1000);
        });
    };

});

mainModule.factory('RequestsErrorHandler', ['$q', '$rootScope', '$timeout', function($q, $rootScope, $timeout) {
    return {
        responseError: function(rejection) {

            var shouldHandle = (rejection && rejection.config && rejection.config.headers);

            if (shouldHandle) {
                console.log(rejection);
                $rootScope.errorMessage = rejection.data ? rejection.data : rejection.statusText;
                $rootScope.errorCode = rejection.status;
                $timeout(function(){$rootScope.errorMessage = ""}, 5000);
            }

            return $q.reject(rejection);
        }
    };
}]);

mainModule.config(['$provide', '$httpProvider', function($provide, $httpProvider) {
    $httpProvider.interceptors.push('RequestsErrorHandler');
}]);


function openUrl(url)
{
    window.open(url, '_blank');
}
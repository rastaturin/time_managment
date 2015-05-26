# Time Management System

This is a test project "Time Management System"

## Demo

Try demo: [time.mobi22.com/view.html](http://time.mobi22.com/view.html)

    Admin
    Login: admin@system 
    Password: 123123

    Manager
    Login: manager@system 
    Password: 123123

    User
    Login: user@system 
    Password: 123123

## Technologies used

* Laravel (with Eloquent ORM)
* MySQL
* AngularJS
* Bootstrap
* PHPUnit
    
### REST API

All user actions used the API.

* Registration `POST: /api/user`
* Login `POST: /api/user/login`
* User list `GET: /api/user`
* Update user `PUT: /api/user/:id`
* Delete user `DELETE: /api/user/:id`
* Add work `POST: /api/user/:userId/timerow`
* Get work list `GET: /api/user/:userId/timerow`
* Update work `PUT: /api/user/:userId/timerow/:id`
* Delete work `DELETE: /api/user/:userId/timerow/:id`

See API details in `Laravel/tests/ApiTest.php`

### Authentication

1. Get a token using `POST: /api/user/login`
2. At every API request put the token to the header `X-Auth-Token` 

### Tests

`Laravel/tests/ApiTest.php`

## Installation

1. Download
2. Setup server endpoint to `Laravel/public`
3. Copy `Laravel/.env.example` to `Laravel/.env.` and set database credentials
4. Run `php Laravel/artisan migrate`


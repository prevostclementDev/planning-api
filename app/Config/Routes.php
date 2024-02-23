<?php

use App\Filters\Auth;
use \App\Filters\Permissions;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// ############################################################
//                              ROUTE
// ############################################################

// AuthController
$routes->post('/connection', 'AuthController::connection');

// SchoolSpacesController
$routes->post('/schoolspaces', 'SchoolSpacesController::create');
$routes->get('/schoolspaces', 'SchoolSpacesController::get' , [ 'filter' => Auth::class ] );
$routes->put('/schoolspaces', 'SchoolSpacesController::update', [
    'filter' => [
        Auth::class,
        'permissions:manage_schoolspace'
    ],
]);
$routes->delete('/schoolspaces', 'SchoolSpacesController::delete', [
    'filter' => [
        Auth::class,
        'permissions:manage_schoolspace'
    ],
]);

// UsersController
$routes->post('/schoolspaces/users','UsersController::create',[
    'filter' => [
        Auth::class,
        'permissions:manage_users'
    ]
]);
$routes->get('/schoolspaces/users','UsersController::get',[
    'filter' => [
        Auth::class,
        'permissions:see_all_users'
    ]
]);
$routes->get('/schoolspaces/users/(:num)','UsersController::getOne/$1',['filter' => [Auth::class]]);
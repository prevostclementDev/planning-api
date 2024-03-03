<?php

use App\Filters\Auth;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// ############################################################
//                              ROUTE
// ############################################################

// AuthController
$routes->post('/connection', 'AuthController::connection');

$routes->group('schoolspaces', static function ($routes) {

    // #########################################################
    //                    SCHOOLSPACES ROUTE
    // #########################################################
    $routes->post('', 'SchoolSpacesController::create');
    $routes->get('', 'SchoolSpacesController::get' , [ 'filter' => Auth::class ] );
    $routes->put('', 'SchoolSpacesController::update', [
        'filter' => [
            Auth::class,
            'permissions:manage_schoolspace'
        ],
    ]);
    $routes->delete('', 'SchoolSpacesController::delete', [
        'filter' => [
            Auth::class,
            'permissions:manage_schoolspace'
        ],
    ]);

    // #########################################################
    //                          USERS
    // #########################################################
    $routes->group('users', ['namespace' => 'App\Controllers\Users'], static function ($routes) {

        // USERS ROUTE
        $routes->post('','UsersController::create',[
            'filter' => [
                Auth::class,
                'permissions:manage_users'
            ]
        ]);
        $routes->get('','UsersController::get',[
            'filter' => [
                Auth::class,
                'permissions:see_all_users'
            ]
        ]);
        $routes->get('(:num)','UsersController::getOne/$1',['filter' => [Auth::class]]);
        $routes->put('(:num)','UsersController::update/$1',['filter' => [Auth::class]]);
        $routes->delete('(:num)','UsersController::delete/$1',['filter' => [
            Auth::class,
            'permissions:manage_users'
        ]]);

        // USER PERMISSIONS
        $routes->get('(:num)/permissions','UsersController::getPermissions/$1',['filter' => [Auth::class]]);

        // USER UNAVAILABILITY
        $routes->get('(:num)/unavailabilities','UnavailabilitiesController::get/$1', ['filter' => [ Auth::class ]]);
        $routes->post('(:num)/unavailabilities','UnavailabilitiesController::create/$1', ['filter' => [ Auth::class ]]);
        $routes->put('(:num)/unavailabilities/(:num)','UnavailabilitiesController::update/$1/$2', ['filter' => [ Auth::class ]]);
        $routes->delete('(:num)/unavailabilities/(:num)','UnavailabilitiesController::delete/$1/$2', ['filter' => [ Auth::class ]]);

        // USER SKILL
        $routes->post('(:num)/skills/(:num)','TeacherSkillController::create/$1/$2', ['filter' => [ Auth::class ]]);
        $routes->get('(:num)/skills','TeacherSkillController::get/$1', [ 'filter' => [ Auth::class ]]);
        $routes->delete('(:num)/skills/(:num)','TeacherSkillController::delete/$1/$2', [ 'filter' => [ Auth::class ]]);

    });

    // #########################################################
    //                        COURSES
    // #########################################################
    $routes->group('courses', static function ($routes) {

       $routes->post('','CoursesController::create',['filter' => [
           Auth::class,
           'permissions:manage_courses'
       ]]);
       $routes->get('','CoursesController::get',['filter' => [
           Auth::class,
           'permissions:manage_courses'
       ]]);
       $routes->get('(:num)','CoursesController::getOne/$1',[ 'filter' => [
           Auth::class,
           'permissions:manage_courses'
       ] ]);
       $routes->put('(:num)','CoursesController::update/$1',[ 'filter' => [
           Auth::class,
           'permissions:manage_courses'
       ] ]);
       $routes->delete('(:num)','CoursesController::delete/$1',[ 'filter' => [
           Auth::class,
           'permissions:manage_courses'
       ] ]);

    });

    // #########################################################
    //                        PROGRAMS
    // #########################################################
    $routes->group('programs', ['namespace' => 'App\Controllers\Programs'], static function ($routes) {

        $routes->post('','ProgramsController::create',['filter' => [
            Auth::class,
            'permissions:manage_programs'
        ]]);
        $routes->get('','ProgramsController::get',['filter' => [
            Auth::class,
            'permissions:manage_programs'
        ]]);
        $routes->get('(:num)','ProgramsController::getOne/$1',[ 'filter' => [
            Auth::class,
            'permissions:manage_programs'
        ] ]);
        $routes->put('(:num)','ProgramsController::update/$1',[ 'filter' => [
            Auth::class,
            'permissions:manage_programs'
        ] ]);
        $routes->delete('(:num)','ProgramsController::delete/$1',[ 'filter' => [
            Auth::class,
            'permissions:manage_programs'
        ] ]);

        // COURSES IN PROGRAMS
        $routes->post('(:num)/courses/(:num)','ProgramsCoursesController::addCourse/$1/$2',['filter' => [
            Auth::class,
            'permissions:manage_programs'
        ]]);
        $routes->get('(:num)/courses','ProgramsCoursesController::getCourse/$1',['filter' => [
            Auth::class,
            'permissions:manage_programs'
        ]]);
        $routes->delete('(:num)/courses/(:num)','ProgramsCoursesController::removeCourse/$1/$2',['filter' => [
            Auth::class,
            'permissions:manage_programs'
        ]]);

    });

    // #########################################################
    //                        CLASS
    // #########################################################
    $routes->group('class', ['namespace' => 'App\Controllers\Class'] , static function ($routes) {

        $routes->post('','ClassController::create',['filter' => [ Auth::class, 'permissions:manage_class']]);
        $routes->get('','ClassController::get',['filter' => [ Auth::class, 'permissions:manage_class']]);
        $routes->get('(:num)','ClassController::getOne/$1',['filter' => [ Auth::class, 'permissions:manage_class']]);
        $routes->delete('(:num)','ClassController::delete/$1',['filter' => [ Auth::class, 'permissions:manage_class']]);
        $routes->put('(:num)','ClassController::update/$1',['filter' => [ Auth::class, 'permissions:manage_class']]);

        // user in class
        $routes->post('(:num)/users/(:num)', 'ClassUserController::add/$1/$2',['filter' => [ Auth::class, 'permissions:manage_class']]);
        $routes->get('(:num)/users', 'ClassUserController::get/$1',['filter' => [ Auth::class, 'permissions:manage_class']]);
        $routes->delete('(:num)/users/(:num)', 'ClassUserController::delete/$1/$2',['filter' => [ Auth::class, 'permissions:manage_class']]);

    });

    // #########################################################
    //                        CLASSROOM
    // #########################################################
    $routes->group('classrooms', static function ($routes) {

        $routes->post('', 'ClassroomController::create',['filter' => [
            Auth::class,
            'permissions:manage_classrooms'
        ]]);
        $routes->get('', 'ClassroomController::get',['filter' => [
            Auth::class,
            'permissions:manage_classrooms'
        ]]);
        $routes->put('(:num)', 'ClassroomController::update/$1',['filter' => [
            Auth::class,
            'permissions:manage_classrooms'
        ]]);
        $routes->delete('(:num)', 'ClassroomController::delete/$1',['filter' => [
            Auth::class,
            'permissions:manage_classrooms'
        ]]);

    });

    // #########################################################
    //                        PLANNING
    // #########################################################
    $routes->group('plannings', ['namespace' => 'App\Controllers\Plannings'] , static function ($routes) {

        $routes->post('','PlanningsController::create',[
            'filter' => [
                Auth::class,
                'permissions:manage_plannings'
            ],
        ]);
        $routes->get('','PlanningsController::get',[ 'filter' => [ Auth::class ]]);
        $routes->get('(:num)','PlanningsController::getOne/$1',['filter' => [ Auth::class ]]);
        $routes->delete('(:num)','PlanningsController::delete/$1',[
            'filter' => [
                Auth::class,
                'permissions:manage_plannings'
            ],
        ]);
        $routes->put('(:num)','PlanningsController::update/$1',[
            'filter' => [
                Auth::class,
                'permissions:manage_plannings'
            ],
        ]);

    });

});

// OVERRIDE 404 ERROR
$routes->set404Override('App\Controllers\ErrorsController::override404');
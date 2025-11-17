<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->post('login/auth', 'Login::auth');

// Admin Dashboard
$routes->get('/adminDashboard', 'AdminDashboard::index', ['filter' => 'auth:admin']);

// Patient API
$routes->group('api/patient', ['filter' => 'auth:admin'], function($routes) {
    $routes->post('add', 'Api\PatientController::add');
    $routes->get('search', 'Api\PatientController::search');
    $routes->put('update/(:num)', 'Api\PatientController::update/$1');
    $routes->delete('delete/(:num)', 'Api\PatientController::delete/$1');
});

$routes->get('patient/profile/(:num)', 'PatientProfile::view/$1', ['filter' => 'auth:admin']);

// Employee API
$routes->group('api/employee', ['filter' => 'auth:admin'], function($routes) {
    $routes->post('add', 'Api\EmployeeController::add');
    $routes->get('search', 'Api\EmployeeController::search');
    $routes->post('update/(:num)', 'Api\EmployeeController::update/$1'); // fixed
    $routes->delete('delete/(:num)', 'Api\EmployeeController::delete/$1');
});

$routes->get('employee/profile/(:num)', 'EmployeeProfile::view/$1', ['filter' => 'auth:admin']);

// Doctor Dashboard
$routes->group('doctor', ['filter' => 'auth:doctor'], function($routes) {
    $routes->get('dashboard', 'DoctorDashboard::index');
});

// Receptionist Dashboard
$routes->group('reception', ['filter' => 'auth:receptionist'], function($routes) {
    $routes->get('dashboard', 'ReceptionDashboard::index');
});

// Appointments
$routes->group('api/appointments', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('form/(:num)', 'Api\AppointmentController::form/$1');
    $routes->get('getSlots', 'Api\AppointmentController::getSlots');
    $routes->post('save', 'Api\AppointmentController::save');
});

$routes->get('api/patient/getAppointments/(:num)', 'Api\PatientController::getAppointments/$1', ['filter' => 'auth:admin']);

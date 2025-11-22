<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -------------------------
// Public / Login
// -------------------------
$routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->post('login/auth', 'Login::auth');

// -------------------------
// Admin Dashboard
// -------------------------
$routes->get('/adminDashboard', 'AdminDashboard::index', ['filter' => 'auth:admin']);

// -------------------------
// Patient API Routes (admin only)
// -------------------------
$routes->group('api/patient', ['filter' => 'auth:admin'], function($routes) {
    $routes->post('add', 'Api\PatientController::add');
    $routes->get('search', 'Api\PatientController::search');
    $routes->put('update/(:num)', 'Api\PatientController::update/$1');
    $routes->delete('(:num)', 'Api\PatientController::delete/$1');
    $routes->post('delete/(:num)', 'Api\PatientController::delete/$1'); // optional POST delete
    $routes->get('getAppointments/(:num)', 'Api\PatientController::getAppointments/$1');
    $routes->get('generate-mr', 'Api\PatientController::getNextMrNumber');
});

// View patient profile (admin)
$routes->get('patient/profile/(:num)', 'PatientProfile::view/$1', ['filter' => 'auth:admin']);

// -------------------------
// Employee API Routes (admin only)
// -------------------------
$routes->group('api/employee', ['filter' => 'auth:admin'], function($routes) {
    $routes->post('add', 'Api\EmployeeController::add');
    $routes->get('search', 'Api\EmployeeController::search');
    $routes->put('update/(:num)', 'Api\EmployeeController::update/$1');
    $routes->delete('(:num)', 'Api\EmployeeController::delete/$1');
    $routes->post('delete/(:num)', 'Api\EmployeeController::delete/$1');
    $routes->get('checkAppointments/(:num)', 'Api\EmployeeController::checkAppointments/$1');
});

// View employee profile (admin)
$routes->get('employee/profile/(:num)', 'EmployeeProfile::view/$1', ['filter' => 'auth:admin']);

// -------------------------
// Doctor routes (auth only for doctor)
// -------------------------
$routes->group('doctor', ['filter' => 'auth:doctor'], function($routes) {
    $routes->get('dashboard', 'DoctorDashboard::index');
});

// -------------------------
// Receptionist routes (auth only for receptionist)
// -------------------------
$routes->group('reception', ['filter' => 'auth:receptionist'], function($routes) {
    $routes->get('dashboard', 'ReceptionDashboard::index');
});

// -------------------------
// Appointment routes (admin only)
// -------------------------
$routes->get('api/appointments/form/(:num)', 'Api\AppointmentController::form/$1', ['filter' => 'auth:admin']);
$routes->get('api/appointments/getSlots', 'Api\AppointmentController::getSlots', ['filter' => 'auth:admin']);
$routes->post('api/appointments/save', 'Api\AppointmentController::save', ['filter' => 'auth:admin']);

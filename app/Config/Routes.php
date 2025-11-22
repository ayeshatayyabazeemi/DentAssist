<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->post('login/auth', 'Login::auth');

// Only admin can access admin dashboard
$routes->get('/adminDashboard', 'AdminDashboard::index', ['filter' => 'auth:admin']);

// -------------------------
// 👇 Patient API routes
// -------------------------
$routes->post('api/patient/add', 'Api\PatientController::add', ['filter' => 'auth:admin']);
$routes->get('api/patient/search', 'Api\PatientController::search', ['filter' => 'auth:admin']);
$routes->delete('api/patient/(:num)', 'Api\PatientController::delete/$1', ['filter' => 'auth:admin']);
$routes->post('api/patient/delete/(:num)', 'Api\PatientController::delete/$1', ['filter' => 'auth:admin']);
$routes->put('api/patient/update/(:num)', 'Api\PatientController::update/$1', ['filter' => 'auth:admin']); // NEW ROUTE
$routes->get('patient/profile/(:num)', 'PatientProfile::view/$1', ['filter' => 'auth:admin']);

// -------------------------
// 👇 Employee API routes
// -------------------------
$routes->post('api/employee/add', 'Api\EmployeeController::add', ['filter' => 'auth:admin']);
$routes->get('api/employee/search', 'Api\EmployeeController::search', ['filter' => 'auth:admin']);
$routes->delete('api/employee/(:num)', 'Api\EmployeeController::delete/$1', ['filter' => 'auth:admin']);
$routes->post('api/employee/delete/(:num)', 'Api\EmployeeController::delete/$1', ['filter' => 'auth:admin']);
$routes->get('employee/profile/(:num)', 'EmployeeProfile::view/$1', ['filter' => 'auth:admin']);

// -------------------------
// 👇 Only doctors
// -------------------------
$routes->group('doctor', ['filter' => 'auth:doctor'], function($routes) {
    $routes->get('dashboard', 'DoctorDashboard::index');
});

// -------------------------
// 👇 Only receptionists
// -------------------------
$routes->group('reception', ['filter' => 'auth:receptionist'], function($routes) {
    $routes->get('dashboard', 'ReceptionDashboard::index');
});

// ---------------- APPOINTMENT ROUTES ----------------
// Form data for a patient (doctors + existing appointments)
$routes->get('api/appointments/form/(:num)', 'Api\AppointmentController::form/$1', ['filter' => 'auth:admin']);

// Get available slots for a doctor on a date
$routes->get('api/appointments/getSlots', 'Api\AppointmentController::getSlots', ['filter' => 'auth:admin']);

// Save new appointment
$routes->post('api/appointments/save', 'Api\AppointmentController::save', ['filter' => 'auth:admin']);



// Get appointments for a patient
$routes->get('api/patient/getAppointments/(:num)', 'Api\PatientController::getAppointments/$1', ['filter' => 'auth:admin']);


$routes->get('api/patient/generate-mr', 'Api\PatientController::generateMrNumber');
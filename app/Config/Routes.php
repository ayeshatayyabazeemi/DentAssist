<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default
$routes->get('/', 'Landing::index');
// $routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->post('login/auth', 'Login::auth');
$routes->post('logout', 'Login::logout', ['filter' => 'auth:admin']);

// -------------------------
// Admin Dashboard
// -------------------------
$routes->get('/adminDashboard', 'AdminDashboard::index', ['filter' => 'auth:admin']);
$routes->get('/invoice/data', 'PatientProfile::getInvoiceSummary');
$routes->get('/invoice/procedure/data', 'PatientProfile::getProcedureRevenueSummary');
$routes->get('procedures/list', 'PatientProfile::list');

// -------------------------
// invoice
// -------------------------
$routes->get('patient/invoice/(:num)', 'PatientProfile::invoiceView/$1', ['filter' => 'auth:admin,receptionist']);
$routes->post('patient/invoice/save', 'PatientProfile::saveInvoice', ['filter' => 'auth:admin,receptionist']);
$routes->get('patient/procedures', 'PatientProfile::getProcedures', ['filter' => 'auth:admin,receptionist']);

// -------------------------
// procedures
// -------------------------
$routes->get('patient/procedures', 'PatientProfile::getProcedures');
$routes->post('patient/procedures/create', 'PatientProfile::createProcedure');
$routes->post('patient/procedures/update', 'PatientProfile::updateProcedure');
$routes->post('patient/procedures/delete', 'PatientProfile::deleteProcedure');
// -------------------------
// Patient API Routes
// -------------------------
$routes->post('api/patient/add', 'Api\PatientController::add', ['filter' => 'auth:admin']);
$routes->get('api/patient/search', 'Api\PatientController::search', ['filter' => 'auth:admin']);
$routes->put('api/patient/update/(:num)', 'Api\PatientController::update/$1', ['filter' => 'auth:admin']);
$routes->delete('api/patient/(:num)', 'Api\PatientController::delete/$1', ['filter' => 'auth:admin']);
$routes->post('api/patient/delete/(:num)', 'Api\PatientController::delete/$1', ['filter' => 'auth:admin']);
$routes->get('api/patient/getAppointments/(:num)', 'Api\PatientController::getAppointments/$1', ['filter' => 'auth:admin']);
$routes->get('api/patient/generate-mr', 'Api\PatientController::generateMrNumber', ['filter' => 'auth:admin']);

// View patient profile (admin)
$routes->get('patient/profile/(:num)', 'PatientProfile::view/$1', ['filter' => 'auth:admin']);

// -------------------------
// Patient Card PDF Route (TCPDF)
// -------------------------
$routes->get('patientcard/(:num)', 'PatientCardController::generate/$1', ['filter' => 'auth:admin']);

// -------------------------
// Employee API Routes
// -------------------------
$routes->post('api/employee/add', 'Api\EmployeeController::add', ['filter' => 'auth:admin']);
$routes->get('api/employee/search', 'Api\EmployeeController::search', ['filter' => 'auth:admin']);
$routes->put('api/employee/update/(:num)', 'Api\EmployeeController::update/$1', ['filter' => 'auth:admin']);
$routes->delete('api/employee/(:num)', 'Api\EmployeeController::delete/$1', ['filter' => 'auth:admin']);
$routes->post('api/employee/delete/(:num)', 'Api\EmployeeController::delete/$1', ['filter' => 'auth:admin']);

// View employee profile (admin)
$routes->get('employee/profile/(:num)', 'EmployeeProfile::view/$1', ['filter' => 'auth:admin']);
$routes->get('api/employee/checkAppointments/(:num)', 'Api\EmployeeController::checkAppointments/$1', ['filter' => 'auth:admin']);

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

// -------------------------
// Optional: Test barcode route
// -------------------------
$routes->get('barcode-test', 'BarcodeController::test');
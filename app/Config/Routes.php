<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -------------------------
// Default Routes
$routes->get('/', 'Landing::index');
// $routes->get('/', 'Home::index'); // commented from develop branch
$routes->get('/login', 'Login::index');
$routes->post('login/auth', 'Login::auth');
$routes->post('logout', 'Login::logout', ['filter' => 'auth:admin,receptionist']);

// -------------------------
// Admin Dashboard
$routes->get('/adminDashboard', 'AdminDashboard::index', ['filter' => 'auth:admin']);
$routes->get('/receptionDashboard', 'ReceptionController::dashboard', ['filter' => 'auth:admin,receptionist']);
$routes->post('/api/appointments/update-status', 'ReceptionController::updateStatus', ['filter' => 'auth:admin,receptionist']);
$routes->get('/api/appointments/today', 'ReceptionController::fetchTodayAppointments', ['filter' => 'auth:admin,receptionist']);




// -------------------------
// invoice
$routes->get('patient/invoice/(:num)', 'PatientProfile::invoiceView/$1', ['filter' => 'auth:admin,receptionist']);
$routes->post('patient/invoice/save', 'PatientProfile::saveInvoice', ['filter' => 'auth:admin,receptionist']);
$routes->get('patient/procedures', 'PatientProfile::getProcedures', ['filter' => 'auth:admin,receptionist']);

// -------------------------
// procedures
$routes->post('patient/procedures/create', 'PatientProfile::createProcedure');
$routes->post('patient/procedures/update', 'PatientProfile::updateProcedure');
$routes->post('patient/procedures/delete', 'PatientProfile::deleteProcedure');

// -------------------------
// Patient API Routes
// -------------------------
$routes->post('api/patient/add', 'Api\PatientController::add', ['filter' => 'auth:admin,receptionist']);
$routes->get('api/patient/search', 'Api\PatientController::search', ['filter' => 'auth:admin,receptionist']);
$routes->put('api/patient/update/(:num)', 'Api\PatientController::update/$1', ['filter' => 'auth:admin']);
$routes->delete('api/patient/(:num)', 'Api\PatientController::delete/$1', ['filter' => 'auth:admin']);
$routes->post('api/patient/delete/(:num)', 'Api\PatientController::delete/$1', ['filter' => 'auth:admin']);
$routes->get('api/patient/getAppointments/(:num)', 'Api\PatientController::getAppointments/$1', ['filter' => 'auth:admin,receptionist']);
$routes->get('api/patient/generate-mr', 'Api\PatientController::generateMrNumber', ['filter' => 'auth:admin,receptionist']);

// -------------------------
// View patient profile (admin)
$routes->get('patient/profile/(:num)', 'PatientProfile::view/$1', ['filter' => 'auth:admin,receptionist']);

// -------------------------
// Patient Card PDF Route (TCPDF)
// -------------------------
$routes->get('patientcard/(:num)', 'PatientCardController::generate/$1', ['filter' => 'auth:admin,receptionist']);

// -------------------------
// Employee API Routes
$routes->post('api/employee/add', 'Api\EmployeeController::add', ['filter' => 'auth:admin']);
$routes->get('api/employee/search', 'Api\EmployeeController::search', ['filter' => 'auth:admin']);
$routes->put('api/employee/update/(:num)', 'Api\EmployeeController::update/$1', ['filter' => 'auth:admin']);
$routes->delete('api/employee/(:num)', 'Api\EmployeeController::delete/$1', ['filter' => 'auth:admin']);
$routes->post('api/employee/delete/(:num)', 'Api\EmployeeController::delete/$1', ['filter' => 'auth:admin']);

// -------------------------
// View employee profile (admin)
$routes->get('employee/profile/(:num)', 'EmployeeProfile::view/$1', ['filter' => 'auth:admin']);
$routes->get('api/employee/checkAppointments/(:num)', 'Api\EmployeeController::checkAppointments/$1', ['filter' => 'auth:admin']);

// -------------------------
// Lab Orders Routes (Admin Only)
$routes->group('laborders', ['filter' => 'auth:admin'], function($routes) {
    // Web pages
    $routes->get('create', 'LabOrders::create');
    $routes->post('save', 'LabOrders::save');
    $routes->get('history', 'LabOrders::history');

    // Update status from dropdown
    $routes->post('updateStatus', 'LabOrders::updateStatus');

    // DELETE LAB ORDER (NEW ROUTE)
    $routes->post('delete/(:num)', 'LabOrders::delete/$1');

    // Analytics
    $routes->get('analytics', 'LabOrders::analytics');

    // API route for adding lab orders
    $routes->post('api/add', 'Api\LabOrderController::add', ['filter' => 'auth:admin']);
});

// -------------------------
// $routes->group('doctor', ['filter' => 'auth:doctor'], function($routes) {

//     $routes->get('dashboard', 'DoctorDashboard::index');
//     $routes->get('appointments', 'DoctorDashboard::appointments');

//     $routes->get('todayAppointments', 'DoctorDashboard::fetchTodayAppointments');
//     $routes->get('allAppointments', 'DoctorDashboard::fetchAllAppointments');

//     $routes->post('updateStatus', 'DoctorDashboard::updateStatus');

// });

$routes->group('doctor', ['filter' => 'auth:doctor'], function($routes) {
    $routes->get('dashboard', 'DoctorDashboard::index');
    $routes->get('appointments', 'DoctorDashboard::appointments');
    
    // POST route for marking appointment as completed
    $routes->post('updateStatus', 'DoctorDashboard::updateStatus');
});



// -------------------------
// Receptionist Routes
$routes->group('reception', ['filter' => 'auth:receptionist'], function($routes) {
    $routes->get('dashboard', 'ReceptionDashboard::index');
});

// -------------------------
// Appointment routes (admin only)
// -------------------------
$routes->get('api/appointments/form/(:num)', 'Api\AppointmentController::form/$1', ['filter' => 'auth:admin,receptionist']);
$routes->get('api/appointments/getSlots', 'Api\AppointmentController::getSlots', ['filter' => 'auth:admin,receptionist']);
$routes->post('api/appointments/save', 'Api\AppointmentController::save', ['filter' => 'auth:admin,receptionist']);

// -------------------------
// Optional: Test barcode route
$routes->get('barcode-test', 'BarcodeController::test');

// -------------------------
// Frontend AI Assistant route
$routes->get('/ai-assistant', 'AiAssistant::index');


// API route for getting suggestions
$routes->post('/api/ai-assistant', 'Api\AiAssistantController::getSuggestion');
$routes->get('attendance', 'Attendance::index');                 // Daily attendance page
$routes->post('attendance/save', 'Attendance::save');            // Save daily attendance
$routes->get('attendance/monthlyCalendar', 'Attendance::monthlyCalendar'); // Monthly calendar page
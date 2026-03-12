<?php namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PatientModel;

class DoctorDashboard extends BaseController
{
    protected $appointmentModel;
    protected $patientModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
    }

    public function index()
    {
       $doctor_id = session()->get('user_id');

        // Counts
        $pendingCount = $this->appointmentModel->where('employee_id', $doctor_id)->where('status', 'pending')->countAllResults();
        $completedCount = $this->appointmentModel->where('employee_id', $doctor_id)->where('status', 'completed')->countAllResults();

        // Today's appointments
        $today = date('Y-m-d');

    $todaysAppointments = $this->appointmentModel
        ->select('appointments.*, patients.name, patients.mr_number')
        ->join('patients', 'patients.patient_id = appointments.patient_id')
        ->where('appointments.employee_id', $doctor_id)
        ->where('appointments.appointment_date', $today)
        ->findAll();

        return view('doctor/dashboard', [
            'doctor_name' => session()->get('username'),
            'pendingCount' => $pendingCount,
            'completedCount' => $completedCount,
            'todaysAppointments' => $todaysAppointments
        ]);
    }

    public function appointments()
    {
        $doctor_id = session()->get('user_id'); // use this session key

    $appointments = $this->appointmentModel
        ->select('appointments.*, patients.name, patients.mr_number')
        ->join('patients', 'patients.patient_id = appointments.patient_id')
        ->where('appointments.employee_id', $doctor_id)
        ->findAll();

    return view('doctor/appointments', [
        'appointments' => $appointments
    ]);
    }

 public function updateStatus()
{
    $appointment_id = $this->request->getPost('appointment_id');

    if(!$appointment_id) {
        return $this->response->setJSON(['status'=>'error', 'message'=>'Appointment ID missing']);
    }

    // Update appointment status to completed
    $this->appointmentModel
        ->where('appointment_id', $appointment_id)
        ->set(['status' => 'completed'])
        ->update();

    // Return JSON if AJAX
    if($this->request->isAJAX()){
        return $this->response->setJSON(['status'=>'success']);
    }

    // Otherwise redirect back
    return redirect()->to(site_url('doctor/dashboard'));
}
}
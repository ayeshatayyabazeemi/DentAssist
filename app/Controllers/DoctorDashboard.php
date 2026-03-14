<?php namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
use CodeIgniter\API\ResponseTrait;

class DoctorDashboard extends BaseController
{
    use ResponseTrait;

    protected $appointmentModel;
    protected $patientModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
    }

    // Dashboard homepage
    public function index()
    {
        $doctor_id = session()->get('user_id');

        // Counts for KPI boxes
        $pendingCount = $this->appointmentModel
            ->where('employee_id', $doctor_id)
            ->where('status !=', 'completed')
            ->countAllResults();

        $completedCount = $this->appointmentModel
            ->where('employee_id', $doctor_id)
            ->where('status', 'completed')
            ->countAllResults();

        // Today's appointments
        $today = date('Y-m-d');
        $todaysAppointments = $this->appointmentModel
            ->select('appointments.*, patients.name AS patient_name, patients.mr_number')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->where('appointments.employee_id', $doctor_id)
            ->where('appointments.appointment_date', $today)
            ->orderBy('appointments.appointment_time', 'ASC')
            ->findAll();

        return view('doctor/dashboard', [
            'doctor_name' => session()->get('username'),
            'pendingCount' => $pendingCount,
            'completedCount' => $completedCount,
            'todaysAppointments' => $todaysAppointments
        ]);
    }

    // Appointments page (future/remaining appointments)
    public function appointments()
    {
        $doctor_id = session()->get('user_id');
        $today = date('Y-m-d');

        $appointments = $this->appointmentModel
            ->select('appointments.*, patients.name AS patient_name, patients.mr_number')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->where('appointments.employee_id', $doctor_id)
            ->where('appointments.appointment_date >=', $today)
            ->orderBy('appointments.appointment_date', 'ASC')
            ->orderBy('appointments.appointment_time', 'ASC')
            ->findAll();

        return view('doctor/appointments', [
            'appointments' => $appointments
        ]);
    }

    // Update status (AJAX)
    public function updateStatus()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['appointment_id']) || empty($data['status'])) {
            return $this->fail('Invalid data');
        }

        $updated = $this->appointmentModel->update(
            $data['appointment_id'],
            [
                'status' => $data['status'],
                'status_updated_at' => date('Y-m-d H:i:s')
            ]
        );

        if (!$updated) {
            return $this->fail('Failed to update status');
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Appointment status updated'
        ]);
    }
}

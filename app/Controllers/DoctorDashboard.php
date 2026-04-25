<?php namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
use CodeIgniter\Controller;

class DoctorDashboard extends BaseController
{
    protected $appointmentModel;
    protected $patientModel;

    public function __construct()
{
    $this->appointmentModel = new AppointmentModel();
    $this->patientModel = new PatientModel();

    // 🔥 ADD THIS LINE (THIS FIXES YOUR ERROR)
    $this->db = \Config\Database::connect();

    // security check
    if (session()->get('role') !== 'doctor') {
        header("Location: /login");
        exit;
    }
}

    // 🟢 DASHBOARD
    public function index()
    {
        $doctor_id = session()->get('user_id');
        $today = date('Y-m-d');

        // $pendingCount = $this->appointmentModel
        //     ->where('employee_id', $doctor_id)
        //     ->where('status !=', 'completed')
        //     ->countAllResults();

        // $completedCount = $this->appointmentModel
        //     ->where('employee_id', $doctor_id)
        //     ->where('status', 'completed')
        //     ->countAllResults();

        $pendingCount = $this->appointmentModel
    ->where('employee_id', $doctor_id)
    ->where('status !=', 'completed')
    ->where('appointment_date >=', date('Y-m-d'))
    ->countAllResults();

    

    $completedCount = $this->appointmentModel
    ->where('employee_id', $doctor_id)
    ->where('status', 'completed')
    ->where('appointment_date >=', date('Y-m-d', strtotime('-30 days')))
    ->countAllResults();


    

        $todaysAppointments = $this->appointmentModel
    ->select('appointments.*, patients.name AS patient_name, patients.mr_number')
    ->join('patients', 'patients.patient_id = appointments.patient_id')
    ->where('appointments.employee_id', $doctor_id)
    ->where('appointments.appointment_date', $today)
    ->orderBy('appointments.appointment_time', 'ASC')
    ->findAll();

/* ================= UPCOMING (FUTURE) ================= */
$futureAppointments = $this->appointmentModel
    ->select('appointments.*, patients.name AS patient_name, patients.mr_number')
    ->join('patients', 'patients.patient_id = appointments.patient_id')
    ->where('appointments.employee_id', $doctor_id)
    ->where('appointments.appointment_date >', $today)
    ->orderBy('appointments.appointment_date', 'ASC')
    ->findAll();

        /* ================= COMPLETED ================= */
$completedAppointments = $this->appointmentModel
    ->select('appointments.*, patients.name AS patient_name, patients.mr_number')
    ->join('patients', 'patients.patient_id = appointments.patient_id')
    ->where('appointments.employee_id', $doctor_id)
    ->where('appointments.status', 'completed')
    ->orderBy('appointments.appointment_date', 'DESC')
    ->findAll();

return view('doctor/dashboard', [
    'doctor_name' => session()->get('username'),
    'pendingCount' => $pendingCount,
    'completedCount' => $completedCount,

    'todaysAppointments' => $todaysAppointments,
    'futureAppointments' => $futureAppointments,
    'completedAppointments' => $completedAppointments
]);
    }

    // 🟢 PATIENT DETAIL + HISTORY
    public function patient($patient_id)
    {
        $patient = $this->patientModel->find($patient_id);

       // FIXED: real treatment history
        $history = $this->db->table('treatments')
            ->select('treatments.*, procedures.procedure_name')
            ->join('procedures', 'procedures.procedure_id = treatments.procedure_id')
            ->where('patient_id', $patient_id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $procedures = $this->db->table('procedures')->get()->getResultArray();


        return view('doctor/patient_detail', [
            'patient' => $patient,
            'history' => $history,
            'procedures' => $procedures
        ]);
    }

    // 🟢 SAVE TREATMENT (IMPORTANT)
    // 🟢 SAVE TREATMENT (IMPORTANT)
public function saveTreatment()
{
    $data = $this->request->getPost();

    // CHECK if already completed
    $appointment = $this->appointmentModel
        ->where('appointment_id', $data['appointment_id'])
        ->first();

    if ($appointment && $appointment['status'] === 'completed') {
        return redirect()->back()->with('error', 'Already completed. Locked.');
    }

    // Get the selected procedures (may be multiple)
    $procedureIds = $this->request->getPost('procedure_id'); // Array of procedure IDs

    // Ensure it's an array (for safety)
    if (!is_array($procedureIds)) {
        $procedureIds = [$procedureIds]; // Convert to an array if not already
    }

    // Insert each selected procedure into the 'treatments' table
    foreach ($procedureIds as $procId) {
        $this->db->table('treatments')->insert([
            'patient_id'     => $data['patient_id'],
            'appointment_id' => $data['appointment_id'],
            'procedure_id'   => $procId,
            'notes'          => $data['notes'],
            'doctor_name'    => session()->get('username'),
            'created_at'     => date('Y-m-d H:i:s')
        ]);
    }

    // OPTIONAL: auto mark appointment completed
    $this->appointmentModel->update($data['appointment_id'], [
        'status' => 'completed',
        'status_updated_at' => date('Y-m-d H:i:s')
    ]);

    return redirect()->to('/doctor/dashboard');
}

    // 🟢 STATUS UPDATE (already yours but improved)
    public function updateStatus()
    {
        $data = $this->request->getJSON(true);

        $this->appointmentModel->update($data['appointment_id'], [
            'status' => $data['status'],
            'status_updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }
}
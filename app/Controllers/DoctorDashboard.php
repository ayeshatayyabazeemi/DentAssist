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
    // if (session()->get('role') !== 'doctor') {
    //     header("Location: /login");
    //     exit;
    // }

    if (!session()->has('role')) {
    return redirect()->to('/login');
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

    $appointment_id = $this->request->getGet('appointment_id');

    // ================= HISTORY =================
    // $builder = $this->db->table('treatments t')
    //     ->select('t.*, p.procedure_name')
    //     ->join('procedures p', 'p.procedure_id = t.procedure_id', 'left')
    //     ->where('t.patient_id', $patient_id);

    // if (!empty($appointment_id)) {
    //     $builder->where('t.appointment_id', $appointment_id);
    // }

    // $history = $builder
    //     ->orderBy('t.created_at', 'DESC')
    //     ->get()
    //     ->getResultArray();

        // ================= HISTORY =================
$builder = $this->db->table('treatments t')
    ->select('t.*, p.procedure_name, a.appointment_date')
    ->join('procedures p', 'p.procedure_id = t.procedure_id', 'left')
    ->join('appointments a', 'a.appointment_id = t.appointment_id', 'left')
    ->where('t.patient_id', $patient_id);

$history = $builder
    ->orderBy('t.created_at', 'DESC')
    ->get()
    ->getResultArray();


    // ================= PROCEDURES =================
    $procedures = $this->db->table('procedures')
        ->get()
        ->getResultArray();

    // ================= LOCK CHECK =================
    $isLocked = false;

    if (!empty($appointment_id)) {
        $check = $this->appointmentModel
            ->where('appointment_id', $appointment_id)
            ->where('status', 'completed')
            ->first();

        $isLocked = !empty($check);
    }

    return view('doctor/patient_detail', [
        'patient'        => $patient,
        'history'        => $history,
        'procedures'     => $procedures,
        'isLocked'       => $isLocked,
        'appointment_id' => $appointment_id
    ]);
}
  
    // SAVE TREATMENT (IMPORTANT)
public function saveTreatment()
{
    $data = $this->request->getPost();

    $appointmentId = $data['appointment_id'];
    $patientId     = $data['patient_id'];
    $notes         = $data['notes'] ?? '';
    $procedureIds  = $this->request->getPost('procedure_id');

    // ================= CASE 1: NO PROCEDURE (prescription only) =================
    if (empty($procedureIds)) {

        $this->db->table('treatments')->insert([
            'patient_id'     => $patientId,
            'appointment_id' => $appointmentId,
            'procedure_id'   => null,
            'notes'          => $notes,
            'doctor_name'    => session()->get('username'),
            'created_at'     => date('Y-m-d H:i:s'),
            'is_invoiced'    => 0   // ✅ ONLY ADD THIS
        ]);

    } 
    // ================= CASE 2: PROCEDURES SELECTED =================
    else {

        foreach ($procedureIds as $procId) {

            $this->db->table('treatments')->insert([
                'patient_id'     => $patientId,
                'appointment_id' => $appointmentId,
                'procedure_id'   => $procId,
                'notes'          => $notes,
                'doctor_name'    => session()->get('username'),
                'created_at'     => date('Y-m-d H:i:s')
            ]);
        }
    }

    // ================= ALWAYS MARK COMPLETED =================
    $this->appointmentModel->update($appointmentId, [
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

    public function getProceduresByAppointment()
{
    $appointment_id = $this->request->getGet('appointment_id');

    $data = $this->db->table('treatments t')
        ->select('p.procedure_id, p.procedure_name, p.price')
        ->join('procedures p', 'p.procedure_id = t.procedure_id')
        ->where('t.appointment_id', $appointment_id)
        ->get()
        ->getResultArray();

    return $this->response->setJSON($data);
}

}

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

        $pendingCount = $this->appointmentModel
            ->where('employee_id', $doctor_id)
            ->where('status !=', 'completed')
            ->countAllResults();

        $completedCount = $this->appointmentModel
            ->where('employee_id', $doctor_id)
            ->where('status', 'completed')
            ->countAllResults();

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

    // 🟢 PATIENT DETAIL + HISTORY
    // public function patient($patient_id)
    // {
    //     $patient = $this->patientModel->find($patient_id);

    //     // 🟡 Patient history from invoice table
    //     $history = $this->db->table('invoice')
    //         ->where('patient_id', $patient_id)
    //         ->get()
    //         ->getResultArray();

    //     // 🟢 Procedures list
    //     $procedures = $this->db->table('procedures')->get()->getResultArray();

    //     return view('doctor/patient_detail', [
    //         'patient' => $patient,
    //         'history' => $history,
    //         'procedures' => $procedures
    //     ]);
    // }



    public function patient($patient_id, $appointment_id)
{
    $patient = $this->patientModel->find($patient_id);

    $history = $this->db->table('invoice')
        ->where('patient_id', $patient_id)
        ->get()
        ->getResultArray();

    $procedures = $this->db->table('procedures')->get()->getResultArray();

    return view('doctor/patient_detail', [
        'patient' => $patient,
        'history' => $history,
        'procedures' => $procedures,
        'appointment_id' => $appointment_id   // ✅ THIS LINE IS IMPORTANT
    ]);
}

    // 🟢 SAVE TREATMENT (IMPORTANT)
    // public function saveTreatment()
    // {
    //     $data = $this->request->getPost();

    //     // get procedure name
    //     $procedure = $this->db->table('procedures')
    //         ->where('procedure_id', $data['procedure_id'])
    //         ->get()
    //         ->getRowArray();

    //     // insert into invoice (your system style)
    //     $this->db->table('invoice')->insert([
    //         'invoice_id'   => rand(1000,9999),
    //         'patient_id'   => $data['patient_id'],
    //         'patient_name' => $data['patient_name'],
    //         'mr_number'    => $data['mr_number'],
    //         'description'  => $procedure['procedure_name'] . " | " . $data['notes'],
    //         'paid_amount'  => 0,
    //         'dues'         => $procedure['price'],
    //         'advance'      => 0,
    //         'payment_date' => date('Y-m-d'),
    //         'payment_date_new' => date('Y-m-d'),
    //         'user_name' => session()->get('username')
    //     ]);

    //     return redirect()->back()->with('success', 'Treatment Saved');
    // }

   public function saveTreatment()
{
    $data = $this->request->getPost();

    $procedure = $this->db->table('procedures')
        ->where('procedure_id', $data['procedure_id'])
        ->get()
        ->getRowArray();

    // ✅ SAVE INTO TREATMENTS TABLE
    $this->db->table('treatments')->insert([
        'appointment_id' => $data['appointment_id'],
        'patient_id'     => $data['patient_id'],
        'procedure_id'   => $procedure['procedure_id'],
        'procedure_name' => $procedure['procedure_name'],
        'price'          => $procedure['price']
    ]);

    // ✅ ONLY mark completed if clicked
    if ($data['action'] == 'complete') {
        $this->db->table('appointments')
            ->where('appointment_id', $data['appointment_id'])
            ->update([
                'status' => 'completed',
                'status_updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    return redirect()->back()->with('success', 'Treatment Saved');
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
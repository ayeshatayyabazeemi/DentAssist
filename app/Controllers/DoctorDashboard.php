<?php namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\DentalFormModel;
use App\Models\MedicalHistoryModel;
use CodeIgniter\API\ResponseTrait;

class DoctorDashboard extends BaseController
{
    use ResponseTrait;

    protected $appointmentModel;
    protected $patientModel;
    protected $dentalFormModel;
    protected $medicalHistoryModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
        $this->dentalFormModel = new DentalFormModel();
        $this->medicalHistoryModel = new MedicalHistoryModel();
    }

    // Dashboard: Today’s appointments
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

    // Future appointments
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

    // Update appointment status (AJAX)
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

        if (!$updated) return $this->fail('Failed to update status');

        return $this->respond(['status' => 'success', 'message' => 'Appointment status updated']);
    }

    // Open dental form for a specific appointment
    public function dentalForm($appointment_id)
    {
        $appointment = $this->appointmentModel
            ->select('appointments.*, patients.*')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->where('appointments.appointment_id', $appointment_id)
            ->first();

        if (!$appointment) {
            return redirect()->back()->with('error', 'Appointment not found');
        }

        // Load previous dental forms (medical history)
        $history = $this->medicalHistoryModel
            ->where('patient_id', $appointment['patient_id'])
            ->orderBy('visit_date', 'DESC')
            ->findAll();

        return view('doctor/dentalForm', [
            'appointment' => $appointment,
            'history' => $history
        ]);
    }

    // Save dental form submission
    public function saveDentalForm()
    {
        $data = $this->request->getPost();

        $appointment_id = $data['appointment_id'];
        $patient_id = $data['patient_id'];

        $formData = [
            'appointment_id' => $appointment_id,
            'patient_id' => $patient_id,
            'main_complaint' => $data['mainComplaint'] ?? '',
            'treatment_history' => $data['treatmentHistory'] ?? '',
            'medicines' => $data['medicines'] ?? '',
            'allergies' => $data['allergies'] ?? '',
            'pan_chewing' => $data['pan'] ?? 'No',
            'tobacco' => $data['tobacco'] ?? 'No',
            'smoking' => $data['smoking'] ?? 'No',
            'medical_conditions' => isset($data['medical']) ? json_encode($data['medical']) : ''
        ];

        // Insert into dental_forms table
        $this->dentalFormModel->insert($formData);

        // Insert into medical_history table
        $formData['visit_date'] = date('Y-m-d');
        $this->medicalHistoryModel->insert($formData);

        return redirect()->back()->with('success', 'Dental form saved successfully');
    }
}
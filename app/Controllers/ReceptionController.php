<?php namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
class ReceptionController extends BaseController
{
    public function dashboard()
    {
        // Check login + role (if not handled by filter)
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'receptionist') {
            return redirect()->to(base_url('login'));
        }

        return view('reception/dashboard'); 
        // This will load the HTML you’re using 
        // (e.g., engagement UI you created)
    }
    public function fetchTodayAppointments()
    {

        $model = new AppointmentModel();

        $today = date('Y-m-d');
        $builder = $model->builder();

    $builder
        ->select("
            appointments.*,
            patients.name AS patient_name,
            patients.mr_number,
            employee.name AS doctor_name
        ")
        ->join('patients', 'patients.patient_id = appointments.patient_id')
        ->join('employee', 'employee.employee_id = appointments.employee_id')
        ->where("DATE(appointments.appointment_date)", $today)
        ->orderBy('appointments.appointment_time', 'ASC');

    $appointments = $builder->get()->getResultArray();
// log_message('debug', 'Fetched Appointments: ' . json_encode($appointments));
        return $this->response->setJSON($appointments);
    }


    use ResponseTrait;

    public function updateStatus()
    {
        $data = $this->request->getJSON(true);

        if (
            empty($data['appointment_id']) ||
            empty($data['status'])
        ) {
            return $this->fail('Invalid data');
        }

        $appointmentId = $data['appointment_id'];
        $status        = $data['status'];

        $model = new AppointmentModel();

        $updated = $model->update(
            $appointmentId,
            [
                'status' => $status,
                'status_updated_at' => date('Y-m-d H:i:s')
            ]
        );

        if (!$updated) {
            return $this->fail('Failed to update status');
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Appointment status updated'
        ]);
    }
}

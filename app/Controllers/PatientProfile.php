<?php
namespace App\Controllers;

use App\Models\PatientModel;
use App\Models\AppointmentModel;
use CodeIgniter\Controller;

class PatientProfile extends Controller
{
    public function view($id = null)
    {
        if ($id === null) return redirect()->to('/adminDashboard');

        $model = new PatientModel();
        $patient = $model->find($id);

        if (!$patient) {
            return redirect()->to('/adminDashboard')->with('error','Patient not found');
        }

        $appointmentModel = new AppointmentModel();
        $appointments = $appointmentModel
            ->where('patient_id', $id)
            ->join('employee', 'employee.employee_id = appointments.employee_id')
            ->select('appointments.*, employee.name as doctor_name')
            ->orderBy('appointment_date', 'ASC')
            ->findAll();

        return view('patient/patientprofile', [
            'patient' => $patient,
            'appointments' => $appointments
        ]);
    }
}

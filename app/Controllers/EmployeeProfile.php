<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\DoctorScheduleModel;
use App\Models\AppointmentModel;

class EmployeeProfile extends BaseController
{
    protected $employeeModel;
    protected $scheduleModel;
    protected $appointmentModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->scheduleModel = new DoctorScheduleModel();
        $this->appointmentModel = new AppointmentModel();
    }

    public function view($id = null)
    {
        if (empty($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Employee ID required');
        }

        $employee = $this->employeeModel->where('employee_id', $id)->first();
        if (empty($employee)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Employee not found: {$id}");
        }

        $data = ['employee' => $employee];

        // If doctor, fetch schedules and appointments
        if (!empty($employee['is_doctor'])) {
            $empId = $employee['employee_id'];

            // Fetch only schedules with valid start and end times
            $schedules = $this->scheduleModel
                              ->where('employee_id', $empId)
                              ->where('start_time IS NOT NULL')
                              ->where('start_time !=', '')
                              ->where('end_time IS NOT NULL')
                              ->where('end_time !=', '')
                              ->orderBy('day_of_week', 'ASC')
                              ->findAll();
            $data['schedules'] = $schedules;

            // Fetch appointments with patient names
            $appointments = $this->appointmentModel
                                 ->select('appointments.*, patients.name AS patient_name')
                                 ->join('patients', 'patients.patient_id = appointments.patient_id')
                                 ->where('appointments.employee_id', $empId)
                                 ->orderBy('appointments.appointment_date', 'ASC')
                                 ->findAll();
            $data['appointments'] = $appointments;
        }

        return view('employee/employeeprofile', $data);
    }
}

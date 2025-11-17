<?php namespace App\Controllers;

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

    public function view($id=null)
    {
        if(!$id) throw new \CodeIgniter\Exceptions\PageNotFoundException('Employee ID required');
        $emp = $this->employeeModel->find($id);
        if(!$emp) throw new \CodeIgniter\Exceptions\PageNotFoundException("Employee not found");

        $data=['employee'=>$emp];

        if(!empty($emp['is_doctor'])){
            $data['schedules']=$this->scheduleModel->where('employee_id',$id)->orderBy('day_of_week','ASC')->findAll();
            $data['appointments']=$this->appointmentModel
                ->select('appointments.*, patients.name AS patient_name')
                ->join('patients','patients.patient_id = appointments.patient_id')
                ->where('appointments.employee_id',$id)
                ->orderBy('appointments.appointment_date','ASC')
                ->findAll();
        }

        return view('employee/employeeprofile',$data);
    }
}

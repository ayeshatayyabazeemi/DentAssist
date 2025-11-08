<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\DoctorScheduleModel;

class EmployeeProfile extends BaseController
{
    protected $employeeModel;
    protected $scheduleModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->scheduleModel = new DoctorScheduleModel();
    }

    /**
     * Show employee profile page.
     * @param int|null $id Employee ID (primary key)
     */
    public function view($id = null)
    {
        if (empty($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Employee ID required');
        }

        // Fetch employee
        $employee = $this->employeeModel->where('employee_id', $id)->first();
        if (empty($employee)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Employee not found: {$id}");
        }

        $data = ['employee' => $employee];

        // If employee is a doctor, fetch schedule
        if (!empty($employee['is_doctor'])) {
            $schedules = $this->scheduleModel
                              ->where('employee_id', $employee['employee_id'])
                              ->orderBy('day_of_week', 'ASC')
                              ->findAll();
            $data['schedules'] = $schedules;
            $data['appointments'] = []; // optional for view rendering
        }

        return view('employee/employeeprofile', $data);
    }
}

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
     * $id - employee id (primary key in employee table)
     */
    public function view($id = null)
    {
        if (empty($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Employee ID required');
        }

        // fetch employee by primary key 'employee_id'
        $employee = $this->employeeModel->where('employee_id', $id)->first();

        if (empty($employee)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Employee not found: {$id}");
        }

        $data = [
            'employee' => $employee,
        ];

        // If employee is a doctor, fetch schedule and pass to view
        if (!empty($employee['is_doctor'])) {
            $empId = $employee['employee_id'] ?? $id;

            $schedules = $this->scheduleModel
                              ->where('employee_id', $empId)
                              ->orderBy('day_of_week', 'ASC')
                              ->findAll();

            $data['schedules'] = $schedules;

            // pass empty appointments array so the view can render the heading
            $data['appointments'] = [];
        }

        return view('employee/employeeprofile', $data);
    }
}

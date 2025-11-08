<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\DoctorScheduleModel;

class EmployeeController extends BaseController
{
    public function add()
    {
        // Allow only POST
        if ($this->request->getMethod() !== 'POST') {
            return $this->response
                        ->setStatusCode(405)
                        ->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }

        $employeeModel = new EmployeeModel();
        $scheduleModel = new DoctorScheduleModel();

        // Read incoming data (JSON or form)
        $data = $this->request->getJSON(true);
        if (empty($data)) {
            $data = $this->request->getPost([
                'name', 'mobile_no', 'email', 'gender', 'dob', 'address',
                'regdate', 'designation', 'password', 'cnic', 'schedule'
            ]);
        }

        // Trim strings and set empty values to null
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);
                $data[$key] = $value === '' ? null : $value;
            }
        }

        // Basic validation
        if (empty($data['name']) || empty($data['mobile_no']) || empty($data['password'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Name, Mobile No, and Password are required']);
        }

        // Password hashing
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Role flags based on designation
        $data['is_admin'] = ($data['designation'] === 'admin') ? 1 : 0;
        $data['is_doctor'] = ($data['designation'] === 'doctor') ? 1 : 0;
        $data['is_receptionist'] = ($data['designation'] === 'receptionist') ? 1 : 0;
        $data['is_staff'] = ($data['designation'] === 'staff') ? 1 : 0;

        unset($data['designation']); // we’ll store only role flags

        // Insert employee
        $insertID = $employeeModel->insert($data);

        if (!$insertID) {
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Failed to add employee']);
        }

        // If doctor, insert schedule
        if (!empty($data['is_doctor']) && !empty($data['schedule']) && is_array($data['schedule'])) {
            foreach ($data['schedule'] as $day) {
                $scheduleModel->insert([
                    'employee_id' => $insertID,
                    'day_of_week' => $day['day'],
                    'start_time'  => $day['start_time'],
                    'end_time'    => $day['end_time']
                ]);
            }
        }

        return $this->response->setStatusCode(201)
            ->setJSON([
                'status' => 'success',
                'message' => 'Employee added successfully',
                'id' => $insertID
            ]);
    }
    

}
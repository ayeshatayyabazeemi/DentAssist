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
        if (empty($data['name']) || empty($data['mobile_no'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Name and Mobile No are required']);
        }

        // Password hashing
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']); // avoid inserting empty string
        }

        // Role flags based on designation
        $data['is_admin'] = ($data['designation'] === 'admin') ? 1 : 0;
        $data['is_doctor'] = ($data['designation'] === 'doctor') ? 1 : 0;
        $data['is_receptionist'] = ($data['designation'] === 'receptionist') ? 1 : 0;
        $data['is_staff'] = ($data['designation'] === 'staff') ? 1 : 0;

        unset($data['designation']); // store only role flags

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

    public function search() 
    {
        log_message('info', 'API employee search called – query param: {q}', [
            'q' => $this->request->getGet('q')
        ]);

        // Get query param
        $q = trim($this->request->getGet('q'));
        if (!$q) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON(['status'=>'error','message'=>'Search query required']);
        }

        $model = new EmployeeModel();

        // Search by id, mobile_no, email, cnic, name (partial match)
        $results = $model->groupStart()
                         ->like('employee_id', $q)
                         ->orLike('mobile_no', $q)
                         ->orLike('email', $q)
                         ->orLike('cnic', $q)
                         ->orLike('name', $q)
                         ->groupEnd()
                         ->select('employee_id AS id, name, mobile_no, email, cnic')
                         ->findAll(10);  // limit 10

        // Normalize null/empty values to 'none'
        foreach ($results as &$r) {
            $r['email'] = $r['email'] ?: 'none';
            $r['cnic']  = $r['cnic'] ?: 'none';
        }

        return $this->response
                    ->setStatusCode(200)
                    ->setJSON(['status'=>'success','data'=>$results]);
    }
}
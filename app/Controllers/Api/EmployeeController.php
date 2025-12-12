<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\DoctorScheduleModel;
use App\Models\AppointmentModel;

class EmployeeController extends BaseController
{
    protected $employeeModel;
    protected $scheduleModel;
    protected $appointmentModel;

    public function __construct()
    {
        $this->employeeModel    = new EmployeeModel();
        $this->scheduleModel    = new DoctorScheduleModel();
        $this->appointmentModel = new AppointmentModel();
    }

    // ---------------- Add Employee ----------------
    public function add()
{
    if ($this->request->getMethod() !== 'POST') {
        return $this->response->setStatusCode(405)
            ->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
    }

    $data = $this->request->getJSON(true) ?? $this->request->getPost();

    // Trim string fields
    foreach ($data as $k => $v) {
        if (is_string($v)) {
            $data[$k] = trim($v) ?: null;
        }
    }

    // Required fields
    if (empty($data['name']) || empty($data['mobile_no'])) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Name and Mobile Number are required']);
    }

    // ---- Check unique mobile number ----
    $existing = $this->employeeModel
        ->where('mobile_no', $data['mobile_no'])
        ->first();

    if ($existing) {
        return $this->response->setStatusCode(409)
            ->setJSON(['status' => 'error', 'message' => 'This mobile number is already registered.']);
    }

    // Password hashing
    if (!empty($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    } else {
        unset($data['password']);
    }

    // Role flags
    $designation = $data['designation'] ?? '';
    $data['is_admin']        = $designation === 'admin' ? 1 : 0;
    $data['is_doctor']       = $designation === 'doctor' ? 1 : 0;
    $data['is_receptionist'] = $designation === 'receptionist' ? 1 : 0;
    $data['is_staff']        = $designation === 'staff' ? 1 : 0;
    unset($data['designation']);

    // Try–Catch for readable SQL errors
    try {
        $insertID = $this->employeeModel->insert($data);

        if (!$insertID) {
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Could not save employee.']);
        }

        // Doctor schedule
        if (!empty($data['is_doctor']) && !empty($data['schedule']) && is_array($data['schedule'])) {
            foreach ($data['schedule'] as $sch) {
                $this->scheduleModel->insert([
                    'employee_id' => $insertID,
                    'day_of_week' => $sch['day'],
                    'start_time'  => $sch['start_time'],
                    'end_time'    => $sch['end_time']
                ]);
            }
        }

        return $this->response->setStatusCode(201)
            ->setJSON([
                'status'  => 'success',
                'message' => 'Employee added successfully',
                'id'      => $insertID
            ]);
    } 
    catch (\Exception $e) {

        $errorMsg = $e->getMessage();

        // 🔥 Detect readable SQL errors:
        if (strpos($errorMsg, 'Duplicate entry') !== false) {

            if (strpos($errorMsg, 'mobile_no') !== false) {
                $msg = 'Mobile number already exists.';
            } elseif (strpos($errorMsg, 'email') !== false) {
                $msg = 'This email is already registered.';
            } else {
                $msg = 'Duplicate data — the record already exists.';
            }

            return $this->response->setStatusCode(409)
                ->setJSON(['status' => 'error', 'message' => $msg]);
        }

        // Unknown SQL error → show safe message
        return $this->response->setStatusCode(500)
            ->setJSON([
                'status' => 'error',
                'message' => 'Database error: ' . $errorMsg
            ]);
    }
}

    // ---------------- Search Employees ----------------
    public function search()
    {
        $q = trim($this->request->getGet('q'));
        if(!$q) return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Search query required']);

        $results = $this->employeeModel
                        ->groupStart()
                        ->like('employee_id',$q)
                        ->orLike('name',$q)
                        ->orLike('mobile_no',$q)
                        ->orLike('email',$q)
                        ->orLike('cnic',$q)
                        ->groupEnd()
                        ->select('employee_id AS id, name, mobile_no, email, cnic')
                        ->findAll(10);

        foreach($results as &$r){
            $r['email'] = $r['email'] ?: 'none';
            $r['cnic']  = $r['cnic'] ?: 'none';
        }

        return $this->response->setJSON(['status'=>'success','data'=>$results]);
    }

    // ---------------- Update Employee ----------------
    public function update($id=null)
    {
        if(!$id) return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Employee ID required']);
        $employee = $this->employeeModel->find($id);
        if(!$employee) return $this->response->setStatusCode(404)->setJSON(['status'=>'error','message'=>'Employee not found']);

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        // Basic fields
        $fields = ['name','email','mobile_no','gender','dob','cnic','address'];
        $updateData = [];
        foreach($fields as $f) if(isset($data[$f])) $updateData[$f] = trim($data[$f]) ?: null;
        $this->employeeModel->update($id, $updateData);

        // Doctor schedule
        if(!empty($employee['is_doctor']) && !empty($data['schedule']) && is_array($data['schedule'])){
            $this->scheduleModel->where('employee_id',$id)->delete();
            foreach($data['schedule'] as $sch){
                if(!empty($sch['day']) && !empty($sch['start_time']) && !empty($sch['end_time'])){
                    $this->scheduleModel->insert([
                        'employee_id'=>$id,
                        'day_of_week'=>$sch['day'],
                        'start_time'=>$sch['start_time'],
                        'end_time'=>$sch['end_time']
                    ]);
                }
            }
        }

        return $this->response->setJSON(['status'=>'success','message'=>'Employee updated successfully']);
    }

    // ---------------- Delete Employee ----------------
    public function delete($id=null)
    {
        if(!$id) return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Employee ID required']);

        $employee = $this->employeeModel->find($id);
        if(!$employee) return $this->response->setStatusCode(404)->setJSON(['status'=>'error','message'=>'Employee not found']);

        // If doctor, check assigned patients
        if(!empty($employee['is_doctor'])){
            $assigned = $this->appointmentModel->where('employee_id',$id)->first();
            if($assigned){
                return $this->response->setStatusCode(400)->setJSON([
                    'status'=>'error',
                    'message'=>'Cannot delete doctor: assigned to one or more patients'
                ]);
            }
        }

        $this->scheduleModel->where('employee_id',$id)->delete();
        $this->employeeModel->delete($id);

        return $this->response->setJSON(['status'=>'success','message'=>'Employee deleted successfully']);
    }
}

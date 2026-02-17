<?php namespace App\Controllers\Api;

use CodeIgniter\HTTP\IncomingRequest;
use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\AppointmentModel;

class PatientController extends BaseController
{
    public function add()
{
    // Only allow POST
    if ($this->request->getMethod() !== 'POST') {
        return $this->response->setStatusCode(405)
            ->setJSON(['status' => 'error', 'message' => 'Only POST method is allowed.']);
    }

    $model = new PatientModel();

    // Get JSON or POST data
    $data = $this->request->getJSON(true);
    if (empty($data)) {
        $data = $this->request->getPost([
            'name', 'mobile_no', 'email', 'gender', 'dob', 'address',
            'occupation', 'regdate', 'guardianname', 'guardianphonenumber',
            'doctorName', 'cnic', 'bloodGroup', 'insurance', 'mr_number'
        ]);
    }

    // Trim all string values
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $value = trim($value);
            $data[$key] = $value === '' ? null : $value;
        }
    }

    // Required checks
    if (empty($data['name']) || empty($data['mobile_no'])) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Name and Mobile Number are required.']);
    }

    // Duplicate email check (mobile can duplicate)
    if (!empty($data['email'])) {
        $existing = $model->where('email', $data['email'])->first();
        if ($existing) {
            return $this->response->setStatusCode(409) // 409 Conflict
                ->setJSON([
                    'status' => 'error',
                    'message' => 'This email is already registered with another patient.'
                ]);
        }
    }

    // Auto-generate MR number if not provided
    if (empty($data['mr_number']) && !empty($data['insurance'])) {
        $prefix = $data['insurance'];
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT mr_number 
            FROM patients 
            WHERE mr_number LIKE '{$prefix}-%'
            ORDER BY patient_id DESC 
            LIMIT 1
        ");

        $last = $query->getRow();
        $lastNumber = $last ? (int) explode('-', $last->mr_number)[1] : 0;

        $data['mr_number'] = $prefix . '-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    // Try inserting the record
    try {
        $insertID = $model->insert($data);

        if ($insertID) {
            return $this->response->setStatusCode(201)
                ->setJSON([
                    'status' => 'success',
                    'message' => 'Patient added successfully.',
                    'id' => $insertID,
                    'mr_number' => $data['mr_number']
                ]);
        }

        // Insert returned false
        return $this->response->setStatusCode(500)
            ->setJSON(['status' => 'error', 'message' => 'Unable to add patient. Please try again.']);

    } catch (\Exception $e) {

        // Human readable SQL message
        $errorMessage = $e->getMessage();

        // Make MySQL messages friendly
        if (str_contains($errorMessage, 'Duplicate entry')) {
            $errorMessage = 'A record with this information already exists.';
        }

        return $this->response->setStatusCode(500)
            ->setJSON([
                'status' => 'error',
                'message' => $errorMessage
            ]);
    }
}


    

    public function generateMrNumber()
{
    $insurance = $this->request->getGet('insurance'); // Get insurance from query string

    if (!$insurance) {
        return $this->response->setJSON([
            'status' => 'error', 
            'message' => 'Insurance not provided'
        ]);
    }

    $db = \Config\Database::connect();
    
    // Prefix logic: General = GEN, else use insurance code
    $prefix = $insurance === 'GEN' ? 'GEN' : $insurance;

    $query = $db->query("
        SELECT mr_number 
        FROM patients 
        WHERE mr_number LIKE '{$prefix}-%' 
        ORDER BY patient_id DESC 
        LIMIT 1
    ");

    $last = $query->getRow();

    if ($last) {
        $lastNumber = (int) explode('-', $last->mr_number)[1];
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    $mr_number = $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

    return $this->response->setJSON(['mr_number' => $mr_number]);
}


    /* ---------------------------------------------------------
       SEARCH PATIENT
    --------------------------------------------------------- */
    public function search()
    {
        $q = trim($this->request->getGet('q'));
        if (!$q) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status'=>'error','message'=>'Search query required']);
        }

        $model = new PatientModel();
       $escaped_q = $model->escapeLikeString($q);

$order_case = "
CASE 
    WHEN mr_number LIKE '%{$escaped_q}%' THEN 1
WHEN LOWER(name) LIKE LOWER('%{$escaped_q}%') THEN 2
    WHEN mobile_no LIKE '%{$escaped_q}%' THEN 3
    WHEN email LIKE '%{$escaped_q}%' THEN 4
    WHEN cnic LIKE '%{$escaped_q}%' THEN 5
    ELSE 6
END
";


$results = $model->groupStart()
                 ->like('mr_number', $escaped_q)
                 ->orLike('mobile_no', $escaped_q)
                 ->orLike('email', $escaped_q)
                 ->orLike('cnic', $escaped_q)
                 ->orLike('name', $escaped_q)
                 ->groupEnd()
                 ->select('patient_id, mr_number AS id, name, mobile_no, email, cnic')
                 ->orderBy($order_case, 'ASC')                      // MR number matches first
                 ->orderBy('CAST(mr_number AS UNSIGNED)', 'ASC')     // numeric sort
                 ->orderBy('patient_id', 'ASC')                     // fallback
                 ->findAll(10);


        foreach ($results as &$r) {
            $r['email'] = $r['email'] ?: 'none';
            $r['cnic']  = $r['cnic'] ?: 'none';
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON(['status'=>'success','data'=>$results]);
    }

    /* ---------------------------------------------------------
       DELETE PATIENT
    --------------------------------------------------------- */
    public function delete($id = null)
    {
        if (!in_array($this->request->getMethod(), ['DELETE','POST'])) {
            return $this->response->setStatusCode(405)
                ->setJSON(['status'=>'error','message'=>'Method not allowed']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status'=>'error','message'=>'Patient ID required']);
        }

        $model = new PatientModel();
        if (!$model->find($id)) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status'=>'error','message'=>'Patient not found']);
        }

        if ($model->delete($id, true)) {
            return $this->response
                ->setStatusCode(200)
                ->setJSON(['status'=>'success','message'=>'Patient deleted successfully']);
        }

        return $this->response
            ->setStatusCode(500)
            ->setJSON(['status'=>'error','message'=>'Failed to delete patient']);
    }

    /* ---------------------------------------------------------
       UPDATE PATIENT
    --------------------------------------------------------- */
    public function update($id = null)
    {
        if ($this->request->getMethod() !== 'PUT') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['status'=>'error','message'=>'Method not allowed']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status'=>'error','message'=>'Patient ID required']);
        }

        $model = new PatientModel();
        if (!$model->find($id)) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status'=>'error','message'=>'Patient not found']);
        }

        $data = $this->request->getJSON(true);

        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = trim($v) ?: null;
            }
        }

        if ($model->update($id, $data)) {
            return $this->response
                ->setStatusCode(200)
                ->setJSON(['status'=>'success','message'=>'Patient updated successfully']);
        }

        return $this->response
            ->setStatusCode(500)
            ->setJSON(['status'=>'error','message'=>'Failed to update patient']);
    }




    // --------------------------
    // New method: Get patient appointments
    // --------------------------
    public function getAppointments($patientId)
    {
        $appointmentModel = new AppointmentModel();

        $appointments = $appointmentModel
            ->where('patient_id', $patientId)
            ->join('employee', 'employee.employee_id = appointments.employee_id')
            ->select('appointments.*, employee.name as doctor_name')
            ->orderBy('appointment_date', 'ASC')
            ->findAll();

        return $this->response
                    ->setStatusCode(200)
                    ->setJSON([
                        'status' => 'success',
                        'appointments' => $appointments
                    ]);
    }
}

<?php namespace App\Controllers\Api;

use CodeIgniter\HTTP\IncomingRequest;
use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\AppointmentModel;

class PatientController extends BaseController
{
    public function add() 
{
    $method = $this->request->getMethod();

    // Accept only POST
    if ($method !== 'POST') {
        return $this->response
                    ->setStatusCode(405)
                    ->setJSON(['status'=>'error','message'=>'Method not allowed']);
    }

   $model = new PatientModel();

    // Get incoming data (JSON or POST)
    $data = $this->request->getJSON(true);
    if (empty($data)) {
        $data = $this->request->getPost([
            'name','mobile_no','email','gender','dob','address',
            'occupation','regdate','guardianname','guardianphonenumber',
            'doctorName','cnic','bloodGroup','insurance','mr_number' // make sure mr_number is here
        ]);
    }

    // Trim all string values
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $value = trim($value);
            $data[$key] = $value === '' ? null : $value;
        }
    }

    // Validation
    if (empty($data['name']) || empty($data['mobile_no'])) {
        return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['status'=>'error','message'=>'Name & Mobile No required']);
    }

    // ---------------------------
    // Generate MR number if not provided
    // ---------------------------
    if (empty($data['mr_number']) && !empty($data['insurance'])) {
        $prefix = $data['insurance']; // use insurance as prefix
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

    // Insert patient
    $insertID = $model->insert($data);

    if ($insertID) {
        return $this->response
                    ->setStatusCode(201)
                    ->setJSON(['status'=>'success','message'=>'Patient added','id'=>$insertID, 'mr_number' => $data['mr_number']]);
    } else {
        return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['status'=>'error','message'=>'Could not add patient']);
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

    $mr_number = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

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

        $results = $model->groupStart()
                         ->like('patient_id', $q)
                         ->orLike('mobile_no', $q)
                         ->orLike('email', $q)
                         ->orLike('cnic', $q)
                         ->orLike('name', $q)
                         ->groupEnd()
                         ->select('patient_id AS id, name, mobile_no, email, cnic')
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

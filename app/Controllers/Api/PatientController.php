<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\AppointmentModel;

class PatientController extends BaseController
{
    /* ---------------------------------------------------------
       ADD PATIENT
    --------------------------------------------------------- */
    public function add()
    {
        if ($this->request->getMethod() !== 'POST') {
        return $this->response->setStatusCode(405)
            ->setJSON(['status' => 'error', 'message' => 'Only POST allowed']);
    }

    $model = new PatientModel();
    $data = $this->request->getJSON(true) ?? [];

    // Trim string values
    foreach ($data as $k => $v) {
        if (is_string($v)) $data[$k] = trim($v) ?: null;
    }

    // Required fields
    if (empty($data['name']) || empty($data['mobile_no'])) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status'=>'error','message'=>'Name and mobile are required']);
    }

    // Unique email check
    if (!empty($data['email']) && $model->where('email', $data['email'])->first()) {
        return $this->response->setStatusCode(409)
            ->setJSON(['status'=>'error','message'=>'Email already registered']);
    }

    $db = \Config\Database::connect();
    $builder = $db->table('patients');

    // ---------- MR NUMBER LOGIC ----------
    if (empty($data['insurance']) || strtoupper($data['insurance']) === 'GEN') {
        // General Patient (PP)
        $prefix = 'PP';
    } else {
        // Insurance Patient → first 3 letters of company
        $company = preg_replace('/\s+/', '', $data['insurance']); // remove spaces
        $prefix = strtoupper(substr($company, 0, 3));
    }

    // Find last MR number for this prefix
    $builder->select('mr_number')->like('mr_number', $prefix, 'after')
            ->orderBy('patient_id', 'DESC')->limit(1);
    $row = $builder->get()->getRow();

    if ($row) {
        // Increment from last number in DB for this prefix
        $lastNumber = (int) substr($row->mr_number, strlen($prefix));
    } else {
        // Start fresh from 1 if no existing MR for this prefix
        $lastNumber = 0;
    }

    $nextNumber = $lastNumber + 1;

    // Format MR number: prefix + 6-digit number with leading zeros
    $data['mr_number'] = $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        /* -------------------------------------------------
           INSERT
        ------------------------------------------------- */
        try {
            $id = $model->insert($data);

            if ($id) {
                return $this->response->setStatusCode(201)->setJSON([
                    'status' => 'success',
                    'message' => 'Patient added successfully',
                    'id' => $id,
                    'mr_number' => $data['mr_number']
                ]);
            }

            return $this->response->setStatusCode(500)
                ->setJSON(['status'=>'error','message'=>'Insert failed']);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['status'=>'error','message'=>$e->getMessage()]);
        }
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
       UPDATE PATIENT
    --------------------------------------------------------- */
    public function update($id = null)
    {
        if ($this->request->getMethod() !== 'PUT') {
            return $this->response->setStatusCode(405)
                ->setJSON(['status'=>'error','message'=>'PUT only']);
        }

        $model = new PatientModel();
        if (!$model->find($id)) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status'=>'error','message'=>'Patient not found']);
        }

        $data = $this->request->getJSON(true) ?? [];

        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = trim($v) ?: null;
            }
        }

        $model->update($id, $data);

        return $this->response->setJSON([
            'status'=>'success',
            'message'=>'Patient updated'
        ]);
    }

    /* ---------------------------------------------------------
       DELETE PATIENT
    --------------------------------------------------------- */
    public function delete($id = null)
    {
        $model = new PatientModel();

        if (!$model->find($id)) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status'=>'error','message'=>'Patient not found']);
        }

        $model->delete($id, true);

        return $this->response->setJSON([
            'status'=>'success',
            'message'=>'Patient deleted'
        ]);
    }

    /* ---------------------------------------------------------
       PATIENT APPOINTMENTS
    --------------------------------------------------------- */
    public function getAppointments($patientId)
    {
        $appointments = (new AppointmentModel())
            ->where('patient_id', $patientId)
            ->join('employee', 'employee.employee_id = appointments.employee_id')
            ->select('appointments.*, employee.name as doctor_name')
            ->orderBy('appointment_date','ASC')
            ->findAll();

        return $this->response->setJSON([
            'status'=>'success',
            'appointments'=>$appointments
        ]);
    }
}

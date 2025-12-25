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

        // Get JSON data
        $data = $this->request->getJSON(true) ?? [];

        // Trim values
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = trim($v) ?: null;
            }
        }

        // Required fields
        if (empty($data['name']) || empty($data['mobile_no'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status'=>'error','message'=>'Name and mobile are required']);
        }

        // Unique email check
        if (!empty($data['email'])) {
            if ($model->where('email', $data['email'])->first()) {
                return $this->response->setStatusCode(409)
                    ->setJSON([
                        'status'=>'error',
                        'message'=>'Email already registered'
                    ]);
            }
        }

        /* -------------------------------------------------
           MR NUMBER GENERATION (PP00001 / ABC00002)
        ------------------------------------------------- */
        $db = \Config\Database::connect();

        // Decide prefix
       // Decide MR prefix
if (empty($data['insurance']) || strtoupper($data['insurance']) === 'GEN') {
    // General patient
    $prefix = 'PP';
} else {
    // Insurance patient → first 3 letters
    $prefix = strtoupper(substr(
        preg_replace('/\s+/', '', $data['insurance']),
        0,
        3
    ));
}


        // Fetch last MR for prefix
        $builder = $db->table('patients');
        $builder->select('mr_number');
        $builder->like('mr_number', $prefix, 'after');
        $builder->orderBy('patient_id', 'DESC');
        $builder->limit(1);

        $row = $builder->get()->getRow();

        $lastNumber = 0;
        if ($row) {
            $lastNumber = (int) substr($row->mr_number, strlen($prefix));
        }

        $data['mr_number'] = $prefix . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

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
            return $this->response->setStatusCode(400)
                ->setJSON(['status'=>'error','message'=>'Search query required']);
        }

        $model = new PatientModel();
        $escaped = $model->escapeLikeString($q);

        $results = $model
            ->groupStart()
                ->like('mr_number', $escaped)
                ->orLike('name', $escaped)
                ->orLike('mobile_no', $escaped)
                ->orLike('email', $escaped)
                ->orLike('cnic', $escaped)
            ->groupEnd()
            ->select('patient_id, mr_number, name, mobile_no, email, cnic')
            ->orderBy('patient_id','DESC')
            ->findAll(10);

        return $this->response->setJSON([
            'status'=>'success',
            'data'=>$results
        ]);
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

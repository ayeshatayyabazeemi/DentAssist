<?php 
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PatientModel;

class PatientController extends BaseController
{
    public function add()
    {
        // Accept only POST
        if ($this->request->getMethod() !== 'POST') {
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
                'doctorName','cnic','bloodGroup','insurance'
            ]);
        }

        // Trim strings and convert empty strings to null
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

        // Insert patient
        $insertID = $model->insert($data);

        if ($insertID) {
            return $this->response
                         ->setStatusCode(201)
                         ->setJSON(['status'=>'success','message'=>'Patient added','id'=>$insertID]);
        } else {
            return $this->response
                         ->setStatusCode(500)
                         ->setJSON(['status'=>'error','message'=>'Could not add patient']);
        }
    }

    public function search()
    {
        // Get query param
        $q = trim($this->request->getGet('q'));
        if (!$q) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON(['status'=>'error','message'=>'Search query required']);
        }

        $model = new PatientModel();

        // Search by id, mobile_no, email, cnic, name (partial match)
        $results = $model->groupStart()
                         ->like('patient_id', $q)
                         ->orLike('mobile_no', $q)
                         ->orLike('email', $q)
                         ->orLike('cnic', $q)
                         ->orLike('name', $q)
                         ->groupEnd()
                         ->select('patient_id AS id, name, mobile_no, email, cnic')
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

    public function delete($id = null)
    {
        if(!in_array($this->request->getMethod(),['DELETE','POST'])) {
            return $this->response->setStatusCode(405)
                                  ->setJSON(['status'=>'error','message'=>'Method not allowed']);
        }

        if(!$id) return $this->response->setStatusCode(400)
                                       ->setJSON(['status'=>'error','message'=>'Patient ID required']);

        $model = new PatientModel();
        $patient = $model->find($id);

        if(!$patient) return $this->response->setStatusCode(404)
                                            ->setJSON(['status'=>'error','message'=>'Patient not found']);

        if($model->delete($id,true)) {
            return $this->response->setStatusCode(200)
                                  ->setJSON(['status'=>'success','message'=>'Patient deleted successfully']);
        }

        return $this->response->setStatusCode(500)
                              ->setJSON(['status'=>'error','message'=>'Failed to delete patient']);
    }

    public function update($id = null)
    {
        if($this->request->getMethod() !== 'PUT') {
            return $this->response->setStatusCode(405)
                                  ->setJSON(['status'=>'error','message'=>'Method not allowed']);
        }

        if(!$id) return $this->response->setStatusCode(400)
                                       ->setJSON(['status'=>'error','message'=>'Patient ID required']);

        $model = new PatientModel();
        $patient = $model->find($id);
        if(!$patient) return $this->response->setStatusCode(404)
                                            ->setJSON(['status'=>'error','message'=>'Patient not found']);

        $data = $this->request->getJSON(true);
        foreach($data as $k => $v) {
            if(is_string($v)) $data[$k] = trim($v) ?: null;
        }

        if($model->update($id,$data)) {
            return $this->response->setStatusCode(200)
                                  ->setJSON(['status'=>'success','message'=>'Patient updated successfully']);
        }

        return $this->response->setStatusCode(500)
                              ->setJSON(['status'=>'error','message'=>'Failed to update patient']);
    }
}

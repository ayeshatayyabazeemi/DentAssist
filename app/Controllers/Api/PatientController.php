<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PatientModel;

class PatientController extends BaseController
{
    public function add()
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setStatusCode(405)
                         ->setJSON(['status'=>'error','message'=>'Method not allowed']);
        }

        $model = new PatientModel();
        $data = $this->request->getJSON(true) ?: $this->request->getPost([
            'name','mobile_no','email','gender','dob','address',
            'occupation','regdate','guardianname','guardianphonenumber',
            'guardianrelation','doctorName','cnic','bloodGroup','insurance'
        ]);

        foreach ($data as $key => $value) { if(is_string($value)) $data[$key]=trim($value)?:null; }

        if (empty($data['name']) || empty($data['mobile_no'])) {
            return $this->response->setStatusCode(400)
                         ->setJSON(['status'=>'error','message'=>'Name & Mobile No required']);
        }

        $insertID = $model->insert($data);

        if ($insertID) {
            return $this->response->setStatusCode(201)
                         ->setJSON(['status'=>'success','message'=>'Patient added','id'=>$insertID]);
        }

        return $this->response->setStatusCode(500)
                     ->setJSON(['status'=>'error','message'=>'Could not add patient']);
    }

    public function search()
    {
        $q = trim($this->request->getGet('q'));
        if (!$q) return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Search query required']);

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

        foreach($results as &$r){ $r['email']=$r['email']?:'none'; $r['cnic']=$r['cnic']?:'none'; }

        return $this->response->setStatusCode(200)
                     ->setJSON(['status'=>'success','data'=>$results]);
    }

    public function delete($id=null)
    {
        if(!in_array($this->request->getMethod(),['DELETE','POST']))
            return $this->response->setStatusCode(405)->setJSON(['status'=>'error','message'=>'Method not allowed']);

        if(!$id) return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Patient ID required']);

        $model = new PatientModel();
        $patient = $model->find($id);

        if(!$patient) return $this->response->setStatusCode(404)->setJSON(['status'=>'error','message'=>'Patient not found']);

        if($model->delete($id,true)) return $this->response->setStatusCode(200)->setJSON(['status'=>'success','message'=>'Patient deleted successfully']);

        return $this->response->setStatusCode(500)->setJSON(['status'=>'error','message'=>'Failed to delete patient']);
    }

    // ✅ NEW UPDATE METHOD
    public function update($id=null)
    {
        if($this->request->getMethod()!=='PUT') {
            return $this->response->setStatusCode(405)
                ->setJSON(['status'=>'error','message'=>'Method not allowed']);
        }

        if(!$id) return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Patient ID required']);

        $model = new PatientModel();
        $patient = $model->find($id);
        if(!$patient) return $this->response->setStatusCode(404)->setJSON(['status'=>'error','message'=>'Patient not found']);

        $data = $this->request->getJSON(true);
        foreach($data as $k=>$v){ if(is_string($v)) $data[$k]=trim($v)?:null; }

        if($model->update($id,$data)){
            return $this->response->setStatusCode(200)->setJSON(['status'=>'success','message'=>'Patient updated successfully']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status'=>'error','message'=>'Failed to update patient']);
    }
}

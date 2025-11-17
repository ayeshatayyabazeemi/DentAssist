<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\AppointmentModel;

class PatientController extends BaseController
{
    public function search()
    {
        $q = trim($this->request->getGet('q'));
        if(!$q) return $this->response->setJSON(['status'=>'error','message'=>'Search required']);

        $model = new PatientModel();
        $results = $model->groupStart()
            ->like('patient_id', $q)
            ->orLike('name', $q)
            ->orLike('mobile_no', $q)
            ->orLike('email', $q)
            ->orLike('cnic', $q)
            ->groupEnd()
            ->select('patient_id AS id,name,mobile_no,email,cnic')
            ->findAll(10);

        foreach($results as &$r){
            $r['email'] = $r['email'] ?: 'none';
            $r['cnic'] = $r['cnic'] ?: 'none';
        }

        return $this->response->setJSON(['status'=>'success','data'=>$results]);
    }

    public function update($id = null)
    {
        if(!$id) return $this->response->setJSON(['status'=>'error','message'=>'ID required']);

        $model = new PatientModel();
        $patient = $model->find($id);
        if(!$patient) return $this->response->setJSON(['status'=>'error','message'=>'Patient not found']);

        $data = $this->request->getJSON(true) ?: $this->request->getPost([
            'name','email','mobile_no','gender','dob','address','occupation',
            'guardianname','guardianphonenumber','guardianrelation','insurance','doctorName','cnic'
        ]);

        foreach($data as $k=>$v){ if(is_string($v)) $data[$k]=trim($v) ?: null; }

        if(!$model->update($id,$data))
            return $this->response->setJSON(['status'=>'error','message'=>'Failed to update patient']);

        return $this->response->setJSON(['status'=>'success','message'=>'Patient updated successfully']);
    }

    public function delete($id = null)
    {
        if(!$id) return $this->response->setJSON(['status'=>'error','message'=>'ID required']);

        $model = new PatientModel();
        $patient = $model->find($id);
        if(!$patient) return $this->response->setJSON(['status'=>'error','message'=>'Patient not found']);

        if($model->delete($id))
            return $this->response->setJSON(['status'=>'success','message'=>'Patient deleted successfully']);

        return $this->response->setJSON(['status'=>'error','message'=>'Failed to delete patient']);
    }

    public function getAppointments($id = null)
    {
        if(!$id) return $this->response->setJSON(['status'=>'error','message'=>'ID required']);

        $appointmentModel = new AppointmentModel();
        $appointments = $appointmentModel
            ->where('patient_id', $id)
            ->join('employee', 'employee.employee_id = appointments.employee_id')
            ->select('appointments.*, employee.name as doctor_name')
            ->orderBy('appointment_date', 'ASC')
            ->findAll();

        return $this->response->setJSON(['status'=>'success','data'=>$appointments]);
    }
}

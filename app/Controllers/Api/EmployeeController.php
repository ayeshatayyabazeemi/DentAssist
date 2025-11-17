<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\DoctorScheduleModel;

class EmployeeController extends BaseController
{
    public function add()
    {
        $employeeModel = new EmployeeModel();
        $scheduleModel = new DoctorScheduleModel();

        $data = $this->request->getJSON(true) ?: $this->request->getPost([
            'name','mobile_no','email','gender','dob','address','regdate','designation','password','cnic','schedule'
        ]);

        foreach($data as $k=>$v) { if(is_string($v)) $data[$k]=trim($v)?:null; }

        if(empty($data['name']) || empty($data['mobile_no'])) {
            return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Name and Mobile required']);
        }

        if(!empty($data['password'])) $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        else unset($data['password']);

        $data['is_admin'] = ($data['designation']=='admin')?1:0;
        $data['is_doctor'] = ($data['designation']=='doctor')?1:0;
        $data['is_receptionist'] = ($data['designation']=='receptionist')?1:0;
        $data['is_staff'] = ($data['designation']=='staff')?1:0;
        unset($data['designation']);

        $insertID = $employeeModel->insert($data);
        if(!$insertID) return $this->response->setStatusCode(500)->setJSON(['status'=>'error','message'=>'Failed to add employee']);

        // Doctor schedule
        if(!empty($data['is_doctor']) && !empty($data['schedule']) && is_array($data['schedule'])){
            foreach($data['schedule'] as $day){
                if(!empty($day['start_time']) && !empty($day['end_time'])){
                    $scheduleModel->insert([
                        'employee_id'=>$insertID,
                        'day_of_week'=>$day['day'],
                        'start_time'=>$day['start_time'],
                        'end_time'=>$day['end_time']
                    ]);
                }
            }
        }

        return $this->response->setStatusCode(201)->setJSON(['status'=>'success','message'=>'Employee added','id'=>$insertID]);
    }

    public function search()
    {
        $q = trim($this->request->getGet('q'));
        if(!$q) return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'Search required']);

        $model = new EmployeeModel();
        $results = $model->groupStart()
            ->like('employee_id',$q)
            ->orLike('mobile_no',$q)
            ->orLike('email',$q)
            ->orLike('cnic',$q)
            ->orLike('name',$q)
            ->groupEnd()
            ->select('employee_id AS id,name,mobile_no,email,cnic')
            ->findAll(10);

        foreach($results as &$r){
            $r['email']=$r['email']?:'none';
            $r['cnic']=$r['cnic']?:'none';
        }

        return $this->response->setStatusCode(200)->setJSON(['status'=>'success','data'=>$results]);
    }

    public function delete($id=null)
    {
        if(!$id) return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'ID required']);
        $model = new EmployeeModel();
        $emp = $model->find($id);
        if(!$emp) return $this->response->setStatusCode(404)->setJSON(['status'=>'error','message'=>'Not found']);

        if(!empty($emp['is_doctor'])){
            $sched = new DoctorScheduleModel();
            $sched->where('employee_id',$id)->delete();
        }

        if($model->delete($id)) return $this->response->setJSON(['status'=>'success','message'=>'Deleted successfully']);
        return $this->response->setStatusCode(500)->setJSON(['status'=>'error','message'=>'Failed to delete']);
    }

    public function update($id=null)
    {
        if(!$id) return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'ID required']);
        $model = new EmployeeModel();
        $schedModel = new DoctorScheduleModel();
        $emp = $model->find($id);
        if(!$emp) return $this->response->setStatusCode(404)->setJSON(['status'=>'error','message'=>'Employee not found']);

        $data = $this->request->getJSON(true) ?: $this->request->getPost([
            'name','email','mobile_no','gender','dob','cnic','address','schedule'
        ]);

        foreach($data as $k=>$v){ if(is_string($v)) $data[$k]=trim($v)?:null; }

        if(!$model->update($id,$data))
            return $this->response->setStatusCode(500)->setJSON(['status'=>'error','message'=>'Failed to update']);

        // Doctor schedule
        if(!empty($emp['is_doctor']) && !empty($data['schedule']) && is_array($data['schedule'])){
            $schedModel->where('employee_id',$id)->delete();
            foreach($data['schedule'] as $sch){
                if(!empty($sch['start_time']) && !empty($sch['end_time'])){
                    $schedModel->insert([
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
}

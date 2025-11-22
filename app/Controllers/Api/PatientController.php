<?php 
namespace App\Controllers\Api;

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
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['status'=>'error','message'=>'Method not allowed']);
        }

        $model = new PatientModel();

        $data = $this->request->getJSON(true);
        if (empty($data)) {
            $data = $this->request->getPost([
                'name','mobile_no','email','gender','dob','address',
                'occupation','regdate','guardianname','guardianphonenumber',
                'doctorName','cnic','bloodGroup','insurance','mr_number'
            ]);
        }

        // Trim strings / empty to null
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $v = trim($v);
                $data[$k] = $v === '' ? null : $v;
            }
        }

        // Generate MR number if empty
        if (empty($data['mr_number'])) {
            $insuranceCode = strtoupper($data['insurance'] ?? 'GEN');
            $lastPatient = $model->where('insurance', $insuranceCode)
                                 ->orderBy('patient_id', 'DESC')
                                 ->first();

            $newNumber = 1;
            if ($lastPatient && isset($lastPatient['mr_number'])) {
                $parts = explode('-', $lastPatient['mr_number']);
                $lastNumber = isset($parts[1]) ? (int)$parts[1] : 0;
                $newNumber = $lastNumber + 1;
            }

            $data['mr_number'] = sprintf("%s-%03d", $insuranceCode, $newNumber);
        }

        $insertID = $model->insert($data);

        if ($insertID) {
            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'status'=>'success',
                    'message'=>'Patient added',
                    'id'=>$insertID,
                    'mr_number'=>$data['mr_number']
                ]);
        }

        return $this->response
            ->setStatusCode(500)
            ->setJSON(['status'=>'error','message'=>'Could not add patient']);
    }

    /* ---------------------------------------------------------
       GENERATE NEXT MR NUMBER
    --------------------------------------------------------- */
    public function getNextMrNumber()
    {
        $insurance = strtoupper($this->request->getGet('insurance') ?? 'GEN');
        $model = new PatientModel();

        $lastPatient = $model->where('insurance', $insurance)
                             ->orderBy('patient_id', 'DESC')
                             ->first();

        $newNumber = 1;
        if ($lastPatient && isset($lastPatient['mr_number'])) {
            $parts = explode('-', $lastPatient['mr_number']);
            $lastNumber = isset($parts[1]) ? (int)$parts[1] : 0;
            $newNumber = $lastNumber + 1;
        }

        $mr_number = sprintf("%s-%03d", $insurance, $newNumber);

        return $this->response->setJSON([
            'status' => 'success',
            'mr_number' => $mr_number
        ]);
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

    /* ---------------------------------------------------------
       GET PATIENT APPOINTMENTS
    --------------------------------------------------------- */
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

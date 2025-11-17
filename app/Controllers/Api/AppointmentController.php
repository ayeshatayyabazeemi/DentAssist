<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\EmployeeModel;
use App\Models\DoctorScheduleModel;

class AppointmentController extends BaseController
{
    public function form($patientId)
    {
        $employeeModel = new EmployeeModel();
        $appointmentModel = new AppointmentModel();

        $doctors = $employeeModel->where('is_doctor',1)->findAll();

        $appointments = $appointmentModel
            ->where('patient_id',$patientId)
            ->join('employee','employee.employee_id = appointments.employee_id')
            ->select('appointments.*, employee.name as doctor_name')
            ->orderBy('appointment_date','ASC')
            ->findAll();

        return $this->response->setJSON(['patient_id'=>$patientId,'doctors'=>$doctors,'appointments'=>$appointments]);
    }

    public function getSlots()
    {
        $doctorId = $this->request->getGet('doctor_id');
        $date = $this->request->getGet('date');
        if(!$doctorId || !$date) return $this->response->setJSON(['slots'=>[]]);

        $dayName = date('D', strtotime($date));
        $scheduleModel = new DoctorScheduleModel();
        $schedule = $scheduleModel->where('employee_id',$doctorId)->like('day_of_week',$dayName)->first();
        if(!$schedule) return $this->response->setJSON(['slots'=>[]]);

        $start = strtotime($schedule['start_time']);
        $end = strtotime($schedule['end_time']);
        $interval = 1800; // 30 min
        $slots = [];

        for($t=$start;$t<$end;$t+=$interval){
            $slotStart = date('H:i',$t);
            $slotEnd = date('H:i',$t+$interval);
            if(strtotime($slotEnd)>$end) break;

            $exists = (new AppointmentModel())
                ->where('employee_id',$doctorId)
                ->where('appointment_date',$date)
                ->where('appointment_time',$slotStart)
                ->first();
            if(!$exists) $slots[]=['start'=>$slotStart,'display'=>"$slotStart - $slotEnd"];
        }

        return $this->response->setJSON(['slots'=>$slots]);
    }

    public function save()
    {
        if($this->request->getMethod()!=='POST') return $this->response->setStatusCode(405)->setJSON(['status'=>'error','message'=>'Method not allowed']);
        $data = $this->request->getJSON(true);
        if(empty($data['patient_id'])||empty($data['doctor_id'])||empty($data['date'])||empty($data['slot']))
            return $this->response->setStatusCode(400)->setJSON(['status'=>'error','message'=>'All fields are required']);

        $appointmentModel = new AppointmentModel();
        $insertData = [
            'patient_id'=>$data['patient_id'],
            'employee_id'=>$data['doctor_id'],
            'appointment_date'=>$data['date'],
            'appointment_time'=>$data['slot'],
            'slot'=>$data['slot']
        ];

        $insertId = $appointmentModel->insert($insertData);
        if($insertId){
            $appointment = $appointmentModel->join('employee','employee.employee_id = appointments.employee_id')
                ->select('appointments.*, employee.name as doctor_name')
                ->where('appointment_id',$insertId)
                ->first();
            return $this->response->setJSON(['status'=>'success','appointment'=>$appointment]);
        } else {
            return $this->response->setJSON(['status'=>'error','message'=>'Failed to save appointment']);
        }
    }
}

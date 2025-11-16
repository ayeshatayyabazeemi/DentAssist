<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\EmployeeModel;
use App\Models\DoctorScheduleModel;

class AppointmentController extends BaseController
{
    // Fetch form data for a patient (doctors + existing appointments)
    public function form($patientId)
    {
        $employeeModel = new EmployeeModel();
        $appointmentModel = new AppointmentModel();

        // Get all doctors (is_doctor = 1)
        $doctors = $employeeModel->where('is_doctor', 1)->findAll();

        // Get patient's existing appointments with doctor names
        $appointments = $appointmentModel
            ->where('patient_id', $patientId)
            ->join('employee', 'employee.employee_id = appointments.employee_id')
            ->select('appointments.*, employee.name as doctor_name')
            ->orderBy('appointment_date', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'patient_id' => $patientId,
            'doctors' => $doctors,
            'appointments' => $appointments
        ]);
    }

    // AJAX: Get available slots for a doctor on a given date
    // public function getSlots()
    // {
    //     $doctorId = $this->request->getGet('doctor_id');
    //     $date = $this->request->getGet('date');

    //     if (!$doctorId || !$date) {
    //         return $this->response->setJSON(['slots' => []]);
    //     }

    //     // Get the day name (Mon, Tue, etc.) to match doctor_schedule
    //     $dayName = date('D', strtotime($date));

    //     $scheduleModel = new DoctorScheduleModel();
    //     $schedule = $scheduleModel
    //         ->where('employee_id', $doctorId)
    //         ->like('day_of_week', $dayName)
    //         ->first();

    //     if (!$schedule) {
    //         return $this->response->setJSON(['slots' => []]);
    //     }

    //     $start = strtotime($schedule['start_time']);
    //     $end = strtotime($schedule['end_time']);
    //     $slots = [];

    //     // 30-minute slots
    //     for ($t = $start; $t < $end; $t += 1800) {
    //         $slot = date('H:i', $t);

    //         // Check if the slot is already booked
    //         $exists = (new AppointmentModel())
    //             ->where('employee_id', $doctorId)
    //             ->where('appointment_date', $date)
    //             ->where('appointment_time', $slot)
    //             ->first();

    //         if (!$exists) {
    //             $slots[] = $slot;
    //         }
    //     }

    //     return $this->response->setJSON(['slots' => $slots]);
    // }
    // AJAX: Get available slots for a doctor on a given date
public function getSlots()
{
    $doctorId = $this->request->getGet('doctor_id');
    $date     = $this->request->getGet('date');

    if (!$doctorId || !$date) {
        return $this->response->setJSON(['slots' => []]);
    }

    $dayName = date('D', strtotime($date));

    $scheduleModel = new DoctorScheduleModel();
    $schedule = $scheduleModel
        ->where('employee_id', $doctorId)
        ->like('day_of_week', $dayName)
        ->first();

    if (!$schedule) {
        return $this->response->setJSON(['slots' => []]);
    }

    $start = strtotime($schedule['start_time']);
    $end   = strtotime($schedule['end_time']);
    $slots = [];
    $interval = 1800; // 30 minutes

    for ($t = $start; $t < $end; $t += $interval) {
        $slotStart = date('H:i', $t);
        $slotEnd   = date('H:i', $t + $interval);

        if (strtotime($slotEnd) > $end) {
            break;
        }

        // Check if this start time is already booked
        $exists = (new AppointmentModel())
            ->where('employee_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('appointment_time', $slotStart) // store start time in DB
            ->first();

        if (!$exists) {
            $slots[] = [
                'start' => $slotStart,
                'display' => "$slotStart - $slotEnd" // what will show in dropdown
            ];
        }
    }

    return $this->response->setJSON(['slots' => $slots]);
}


    // Save new appointment
    public function save()
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }

        // $data = $this->request->getPost(['patient_id', 'doctor_id', 'date', 'slot']);
        $data = $this->request->getJSON(true);



        // Validate inputs
        if (empty($data['patient_id']) || empty($data['doctor_id']) || empty($data['date']) || empty($data['slot'])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'All fields are required']);
        }

        $appointmentModel = new AppointmentModel();

        $insertData = [
            'patient_id' => $data['patient_id'],
            'employee_id' => $data['doctor_id'],
            'appointment_date' => $data['date'],
            'appointment_time' => $data['slot'],
            'slot' => $data['slot']
        ];

        $insertId = $appointmentModel->insert($insertData);

        if ($insertId) {
            // Return the new appointment with doctor name
            $appointment = $appointmentModel
                ->join('employee', 'employee.employee_id = appointments.employee_id')
                ->select('appointments.*, employee.name as doctor_name')
                ->where('appointment_id', $insertId)
                ->first();

            return $this->response->setJSON(['status' => 'success', 'appointment' => $appointment]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save appointment']);
        }
    }
}

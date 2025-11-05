<?php namespace App\Models;

use CodeIgniter\Model;

class DoctorScheduleModel extends Model
{
    protected $table = 'doctor_schedule';
    protected $primaryKey = 'schedule_id';
    protected $allowedFields = [
        'employee_id','day_of_week','start_time','end_time'
    ];
    protected $useTimestamps = false;
}

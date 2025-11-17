<?php
namespace App\Models;
use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'appointment_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['patient_id','employee_id','appointment_date','appointment_time','slot'];
    protected $validationRules = [
        'patient_id'=>'required|integer',
        'employee_id'=>'required|integer',
        'appointment_date'=>'required|valid_date[Y-m-d]',
        'appointment_time'=>'required',
        'slot'=>'required'
    ];
    protected $skipValidation = false;
}

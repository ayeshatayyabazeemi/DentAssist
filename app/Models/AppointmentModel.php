<?php
namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = 'appointments';           // your appointments table
    protected $primaryKey = 'appointment_id';    // primary key column

    protected $useAutoIncrement = true;          // since appointment_id is INT AUTO_INCREMENT
    protected $returnType = 'array';             // returns results as array
    protected $allowedFields = [
        'patient_id',
        'employee_id',        // doctor
        'appointment_date',
        'appointment_time',
         'status',              // NEW
    'status_updated_at', 
        'slot'                // the slot assigned
    ];

    // Optional validation rules
    protected $validationRules = [
        'patient_id' => 'required|integer',
        'employee_id' => 'required|integer',
        'appointment_date' => 'required|valid_date[Y-m-d]',
        'appointment_time' => 'required',
        
        'slot' => 'required'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
}
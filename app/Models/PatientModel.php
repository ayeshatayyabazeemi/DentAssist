<?php
namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'name','mobile_no','email','gender','dob','address','occupation','regdate',
        'guardianname','guardianphonenumber','guardianrelation','referredBy',
        'doctorName','cnic','bloodGroup','insurance'
    ];
}

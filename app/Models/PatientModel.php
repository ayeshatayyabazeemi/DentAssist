<?php
namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';            // your patients table
    protected $primaryKey = 'patient_id';     // numeric auto_increment id

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'name',
        'mobile_no',
        'email',
        'gender',
        'dob',
        'address',
        'occupation',
        'regdate',
        'guardianname',
        'guardianphonenumber',
        'guardianrelation',
        'referredBy',
        'doctorName',
        'cnic',
        'bloodGroup',
        'insurance',
        'mr_number',
    ];

    protected $validationRules = [
        'name'  => 'required|min_length[2]|max_length[50]',
        'email' => 'permit_empty|valid_email',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
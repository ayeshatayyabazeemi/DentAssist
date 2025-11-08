<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employee';              // your employees table
    protected $primaryKey = 'employee_id';      // primary key column

    protected $useAutoIncrement = true;         // since employee_id is INT AUTO_INCREMENT
    protected $returnType = 'array';            // you can also use 'object' or Entity
    protected $allowedFields = [
        'name',
        'mobile_no',
        'email',
        'gender',
        'dob',
        'cnic',
        'address',
        'regdate',
        'is_admin',
        'is_doctor',
        'is_staff',
        'password',
        'is_receptionist',
        // add other fields if you added more
    ];

    // You can define validation rules, etc.
    protected $validationRules = [
        'email' => 'permit_empty|valid_email',
        'name'  => 'required|min_length[2]|max_length[50]',
        // etc
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
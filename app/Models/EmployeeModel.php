<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employee';
    protected $primaryKey = 'employee_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'name','mobile_no','email','gender','dob','cnic','address','regdate',
        'is_admin','is_doctor','is_staff','is_receptionist','password'
    ];
}

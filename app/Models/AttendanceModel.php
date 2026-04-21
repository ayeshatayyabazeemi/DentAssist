<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'employee_id',
        'date',
        'status',
        'check_in',
        'check_out'
    ];

    protected $useTimestamps = false; // no created_at/updated_at
}
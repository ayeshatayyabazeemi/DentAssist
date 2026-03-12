<?php
namespace App\Models;

use CodeIgniter\Model;

class LabOrderModel extends Model
{
    protected $table = 'lab_orders';
    protected $primaryKey = 'lab_order_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'patient_name',
        'lab_name',
        'lab_item',
        'shade',
        'comments',
        'status',
        'order_date',
        'updated_at'
    ];
}

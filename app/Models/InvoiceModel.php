<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table        = 'invoice';
    protected $primaryKey   = 'id'; // or your PK name
    protected $allowedFields = [
        'invoice_id', 'mr_number', 'patient_id',
        'patient_name', 'description', 'paid_amount',
        'dues', 'advance', 'payment_date', 'user_name'
    ];

    // IMPORTANT: disable automatic timestamps
    protected $useTimestamps = false;
}
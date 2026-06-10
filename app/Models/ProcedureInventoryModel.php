<?php

namespace App\Models;

use CodeIgniter\Model;

class ProcedureInventoryModel extends Model
{
    protected $table = 'procedure_inventory';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'procedure_id',
        'item_id',
        'qty_used'
    ];
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class ProcedureModel extends Model
{
    protected $table = 'procedures';

    // VERY IMPORTANT (You had bugs because of this earlier)
    protected $primaryKey = 'procedure_id';

    protected $allowedFields = [
        'procedure_name',
        'department',
        'price'
    ];

    protected $returnType = 'array';

    protected $useTimestamps = true;
}
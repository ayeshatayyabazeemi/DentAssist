<?php

namespace App\Models;

use CodeIgniter\Model;

class StockTransactionModel extends Model
{
    protected $table = 'stock_transactions';
    protected $primaryKey = 'transaction_id';

    protected $allowedFields = [
        'item_id',
        'type',
        'quantity',
        'note',
        'created_at'
    ];
}
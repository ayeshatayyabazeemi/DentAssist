<?php
namespace App\Models;
use CodeIgniter\Model;

class StockTransactionModel extends Model
{
    protected $table = 'stock_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_id','type','quantity','unit_cost','retail_value','note','created_at'];
    protected $returnType = 'array';
}
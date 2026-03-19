<?php
namespace App\Models;
use CodeIgniter\Model;

class InventorySummaryModel extends Model
{
    protected $table = 'inventory_summary';
    protected $primaryKey = 'item_id';
    protected $allowedFields = ['item_id','available_qty','expired_qty'];
    protected $returnType = 'array';
}
<?php
namespace App\Models;
use CodeIgniter\Model;

class InventoryItemModel extends Model
{
    protected $table = 'inventory_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'item_name','generic_name','barcode','manufacturer','supplier',
        'conversion_unit','reorder_level','unit','rack_number'
    ];
    protected $returnType = 'array';
}
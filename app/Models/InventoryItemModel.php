<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryItemModel extends Model
{
    protected $table = 'inventory_items';
    protected $primaryKey = 'item_id';

    protected $allowedFields = [
        'item_name',
        'generic_name',
        'barcode',
        'manufacturer',
        'supplier_id',
        'conversion_unit',
        'unit_cost',
        'retail_value',
        'reorder_level',
        'available_qty',
        'expired_qty',
        'unit',
        'rack_no'
    ];
}
<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class InventoryController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
// =========================================
    // DASHBOARD
    // =========================================

    public function dashboard()
    {
        $items = $this->db->table('inventory_items')
            ->orderBy('item_name', 'ASC')
            ->get()
            ->getResultArray();

        $totalItems = count($items);

        $lowStock = $this->db->query("
            SELECT COUNT(*) as total
            FROM inventory_items
            WHERE available_qty <= reorder_level
        ")->getRow()->total;

        $outStock = $this->db->query("
            SELECT COUNT(*) as total
            FROM inventory_items
            WHERE available_qty = 0
        ")->getRow()->total;

        $totalValue = $this->db->query("
            SELECT SUM(available_qty * unit_cost) as total
            FROM inventory_items
        ")->getRow()->total;

        return view('inventory/dashboard', [
            'items' => $items,
            'totalItems' => $totalItems,
            'lowStock' => $lowStock,
            'outStock' => $outStock,
            'totalValue' => $totalValue
        ]);
    }
     // =========================================
    // STOCK PAGE
    // =========================================

    public function stockIn()
    {
        $items = $this->db->table('inventory_items')
            ->orderBy('item_name', 'ASC')
            ->get()
            ->getResultArray();

        return view('inventory/stock_in', [
            'items' => $items
        ]);
    }
     // =========================================
    // ADD ITEM
    // =========================================

    public function addItem()
    {
        $data = [
            'item_name' => $this->request->getPost('item_name'),
            'category' => $this->request->getPost('category'),
            'unit' => $this->request->getPost('unit'),
            'reorder_level' => $this->request->getPost('reorder_level'),
            'available_qty' => 0,
            'unit_cost' => $this->request->getPost('unit_cost'),
            'retail_value' => $this->request->getPost('retail_value')
        ];

        $this->db->table('inventory_items')->insert($data);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Item added successfully'
        ]);
    }

    // =========================================
    // ADD STOCK
    // =========================================

    public function addStock()
    {
        $itemId = $this->request->getPost('item_id');

        $qty = (int)$this->request->getPost('quantity');

        $note = $this->request->getPost('note');
         if($qty <= 0){
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid quantity'
            ]);
        }

        // Transaction
        $this->db->table('stock_transactions')->insert([
            'item_id' => $itemId,
            'type' => 'IN',
            'quantity' => $qty,
            'note' => $note,
            'created_at' => date('Y-m-d H:i:s')
        ]);
  // Update Stock
        $this->db->query("
            UPDATE inventory_items
            SET available_qty = available_qty + ?
            WHERE item_id = ?
        ", [$qty, $itemId]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Stock added successfully'
        ]);
    } 
     // =========================================
    // REMOVE STOCK
    // =========================================

    public function removeStock()
    {
        $itemId = $this->request->getPost('item_id');

        $qty = (int)$this->request->getPost('quantity');

        $note = $this->request->getPost('note');

        $item = $this->db->table('inventory_items')
            ->where('item_id', $itemId)
            ->get()
            ->getRowArray();
            if(!$item){
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Item not found'
            ]);
        }

        if($item['available_qty'] < $qty){
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Insufficient stock'
            ]);
        }

        // Save Transaction
        $this->db->table('stock_transactions')->insert([
            'item_id' => $itemId,
            'type' => 'OUT',
            'quantity' => $qty,
            'note' => $note,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Deduct Stock
         $this->db->query("
            UPDATE inventory_items
            SET available_qty = available_qty - ?
            WHERE item_id = ?
        ", [$qty, $itemId]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Stock deducted successfully'
        ]);
    }

    // =========================================
    // TRANSACTIONS
    // =========================================

    public function transactions()
    {
        $transactions = $this->db->table('stock_transactions t')
            ->select('t.*, i.item_name')
            ->join('inventory_items i', 'i.item_id = t.item_id')
            ->orderBy('t.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('inventory/transactions', [
            'transactions' => $transactions
        ]);
    }

    // =========================================
    // REPORTS
    // =========================================

    public function reports()
    {
        $today = date('Y-m-d');

        $usedItems = $this->db->query("
            SELECT
                i.item_name,
                SUM(t.quantity) as total_used,
                i.unit_cost,
                (SUM(t.quantity) * i.unit_cost) as total_cost
            FROM stock_transactions t
            JOIN inventory_items i ON i.item_id = t.item_id
            WHERE t.type = 'OUT'
            AND DATE(t.created_at) = ?
            GROUP BY t.item_id
        ", [$today])->getResultArray();

        $grandTotal = 0;
         foreach($usedItems as $u){
            $grandTotal += $u['total_cost'];
        }

        return view('inventory/reports', [
            'usedItems' => $usedItems,
            'grandTotal' => $grandTotal
        ]);
    }
}
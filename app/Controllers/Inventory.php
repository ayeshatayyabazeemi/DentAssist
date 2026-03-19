<?php
namespace App\Controllers;
use App\Models\InventoryItemModel;
use App\Models\InventorySummaryModel;
use App\Models\StockTransactionModel;

class Inventory extends BaseController
{
    protected $inventoryModel;
    protected $summaryModel;
    protected $transactionModel;

    public function __construct(){
        $this->inventoryModel = new InventoryItemModel();
        $this->summaryModel = new InventorySummaryModel();
        $this->transactionModel = new StockTransactionModel();
    }

    public function index(){
        $search = $this->request->getGet('search');
        $items = $search ? 
            $this->inventoryModel->like('item_name', $search)->orLike('generic_name', $search)->findAll() 
            : $this->inventoryModel->findAll();
        return view('inventory/list',['items'=>$items,'search'=>$search]);
    }

    public function add(){
        if($this->request->getMethod()=='post'){
            $data = $this->request->getPost();
            $inserted = $this->inventoryModel->insert($data);
            if($inserted){
                $this->summaryModel->insert(['item_id'=>$inserted]);
                return redirect()->to('/inventory')->with('success','Item added successfully');
            }
        }
        return view('inventory/form');
    }

    public function edit($id){
        $item = $this->inventoryModel->find($id);
        if(!$item) return redirect()->to('/inventory');
        if($this->request->getMethod()=='post'){
            $data = $this->request->getPost();
            $this->inventoryModel->update($id,$data);
            return redirect()->to('/inventory')->with('success','Item updated successfully');
        }
        return view('inventory/form',['item'=>$item]);
    }

    public function delete($id){
        if($this->request->getMethod()=='post'){
            $this->inventoryModel->delete($id);
            $this->summaryModel->delete($id);
            $this->transactionModel->where('item_id',$id)->delete();
            return redirect()->to('/inventory')->with('success','Item deleted successfully');
        }else{
            return redirect()->to('/inventory')->with('error','Invalid request method!');
        }
    }
}
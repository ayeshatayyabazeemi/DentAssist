<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\LabOrderModel;

class LabOrders extends Controller
{
    protected $labOrderModel;

    public function __construct()
    {
        $this->labOrderModel = new LabOrderModel();
        helper(['form', 'url']);
    }

    // Show Lab Order form
    public function create()
    {
        return view('laborders/create');
    }

    // Save Lab Order
    public function save()
    {
        $patient_name = $this->request->getPost('patient_name');

        if (empty($patient_name)) {
            return redirect()->back()->with('error', 'Please enter a valid patient name.');
        }

        $data = [
            'patient_name' => $patient_name,
            'lab_name'     => $this->request->getPost('lab_name'),
            'lab_item'     => $this->request->getPost('lab_item'),
            'shade'        => $this->request->getPost('shade'),
            'comments'     => $this->request->getPost('comments'),
            'status'       => $this->request->getPost('status') ?? 'Sent',
            'order_date'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        try {
            $this->labOrderModel->insert($data);
            return redirect()->back()->with('success', 'Your order has been successfully placed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    // Lab Order History
    public function history()
    {
        $data['orders'] = $this->labOrderModel->orderBy('lab_order_id','DESC')->findAll();
        return view('laborders/history', $data);
    }
}

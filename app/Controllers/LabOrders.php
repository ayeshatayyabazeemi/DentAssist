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
        $data['statusOptions'] = ['Sent', 'Received', 'Resend', 'Re-Received', 'Completed'];
        return view('laborders/create', $data);
    }

    // Save Lab Order
    public function save()
    {
        $patient_name = $this->request->getPost('patient_name');

        if (empty($patient_name)) {
            return redirect()->back()->with('error', 'Please enter a valid patient name.');
        }

        $status = $this->request->getPost('status');
        $validStatuses = ['Sent', 'Received', 'Resend', 'Re-Received', 'Completed'];
        if (!in_array($status, $validStatuses)) {
            $status = 'Sent';
        }

        $data = [
            'patient_name' => $patient_name,
            'lab_name'     => $this->request->getPost('lab_name'),
            'lab_item'     => $this->request->getPost('lab_item'),
            'shade'        => $this->request->getPost('shade'),
            'comments'     => $this->request->getPost('comments'),
            'status'       => $status,
            'order_date'   => date('Y-m-d H:i:s')
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
        $status = $this->request->getGet('status');
        $query = $this->labOrderModel;

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $data['orders'] = $query->orderBy('lab_order_id', 'ASC')->findAll();
        $data['statusOptions'] = ['Sent', 'Received', 'Resend', 'Re-Received', 'Completed'];

        return view('laborders/history', $data);
    }

    // AJAX: Update Status
    public function updateStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $lab_order_id = $this->request->getPost('lab_order_id');
        $status = $this->request->getPost('status');
        $validStatuses = ['Sent', 'Received', 'Resend', 'Re-Received', 'Completed'];

        if (!$lab_order_id || !$status || !in_array($status, $validStatuses)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid data']);
        }

        try {
            $this->labOrderModel->update($lab_order_id, ['status' => $status]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Status updated']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Delete Lab Order (AJAX)
    public function delete($id = null)
    {
        $response = ['status' => 'error', 'message' => 'Invalid request'];

        if ($id) {
            try {
                $this->labOrderModel->delete($id);
                $response = ['status' => 'success', 'message' => 'Lab order deleted successfully'];
            } catch (\Exception $e) {
                $response = ['status' => 'error', 'message' => 'Failed to delete: ' . $e->getMessage()];
            }
        }

        return $this->response->setJSON($response);
    }

    // -------------------------------
    // Analytics Dashboard
    // -------------------------------
    public function analytics()
    {
        $builder = $this->labOrderModel;

        // Most Ordered Lab Items
        $mostOrderedItems = $builder->select('lab_item, COUNT(*) as count')
                                    ->groupBy('lab_item')
                                    ->orderBy('count', 'DESC')
                                    ->findAll();

        // Most Active Patients
        $mostActivePatients = $builder->select('patient_name, COUNT(*) as count')
                                      ->groupBy('patient_name')
                                      ->orderBy('count', 'DESC')
                                      ->findAll();

        // Orders Per Lab
        $ordersPerLab = $builder->select('lab_name, COUNT(*) as count')
                                 ->groupBy('lab_name')
                                 ->orderBy('count', 'DESC')
                                 ->findAll();

        $data = [
            'mostOrderedItems' => $mostOrderedItems,
            'mostActivePatients' => $mostActivePatients,
            'ordersPerLab' => $ordersPerLab
        ];

        return view('laborders/analytics', $data);
    }
}

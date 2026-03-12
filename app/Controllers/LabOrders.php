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
            'order_date'   => date('Y-m-d H:i:s')
        ];

        try {
            $this->labOrderModel->insert($data);
            return redirect()->back()->with('success', 'Your order has been successfully placed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    // Lab Order History with Status Filter
    public function history()
    {
        $status = $this->request->getGet('status'); // Get filter from query string

        $query = $this->labOrderModel;

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $data['orders'] = $query->orderBy('lab_order_id', 'ASC')->findAll();

        // Pass status options for dropdown
        $data['statusOptions'] = ['Sent', 'Received', 'Completed'];

        return view('laborders/history', $data);
    }

    // Update Status via dropdown
    public function updateStatus()
    {
        $lab_order_id = $this->request->getPost('lab_order_id');
        $status = $this->request->getPost('status');

        if (!$lab_order_id || !$status) {
            return redirect()->back()->with('error', 'Invalid request.');
        }

        try {
            $this->labOrderModel->update($lab_order_id, ['status' => $status]);
            return redirect()->back()->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    // Delete Lab Order (AJAX-supported)
    public function delete($id = null)
    {
        $response = ['status' => 'error', 'message' => 'Invalid request.'];

        if ($id) {
            try {
                $this->labOrderModel->delete($id);
                $response = ['status' => 'success', 'message' => 'Lab order deleted successfully.'];
            } catch (\Exception $e) {
                $response = ['status' => 'error', 'message' => 'Failed to delete: ' . $e->getMessage()];
            }
        }

        // Return JSON for AJAX
        return $this->response->setJSON($response);
    }

    // Optional: Analytics Dashboard (Lab Item / Patient / Lab)
    public function analytics()
    {
        $builder = $this->labOrderModel;

        // Most ordered lab items
        $mostOrderedItems = $builder
            ->select('lab_item, COUNT(*) as count')
            ->groupBy('lab_item')
            ->orderBy('count', 'DESC')
            ->findAll(5);

        // Most active patients
        $mostActivePatients = $builder
            ->select('patient_name, COUNT(*) as count')
            ->groupBy('patient_name')
            ->orderBy('count', 'DESC')
            ->findAll(5);

        // Orders per lab
        $ordersPerLab = $builder
            ->select('lab_name, COUNT(*) as count')
            ->groupBy('lab_name')
            ->orderBy('count', 'DESC')
            ->findAll();

        $data = [
            'mostOrderedItems'   => $mostOrderedItems,
            'mostActivePatients' => $mostActivePatients,
            'ordersPerLab'       => $ordersPerLab,
        ];

        return view('laborders/analytics', $data);
    }
}

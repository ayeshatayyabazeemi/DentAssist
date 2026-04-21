<?php 
namespace App\Controllers\Api;

use CodeIgniter\HTTP\IncomingRequest;
use App\Controllers\BaseController;
use App\Models\LabOrderModel;

class LabOrderController extends BaseController
{
    protected $labOrderModel;

    public function __construct()
    {
        $this->labOrderModel = new LabOrderModel();
        helper(['form', 'url']);
    }

    // Add Lab Order via API
    public function add()
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setStatusCode(405)
                ->setJSON(['status' => 'error', 'message' => 'Only POST method is allowed.']);
        }

        $data = $this->request->getJSON(true);
        if (empty($data)) {
            $data = $this->request->getPost([
                'patient_name', 'lab_name', 'lab_item', 'shade', 'comments', 'status'
            ]);
        }

        // Trim strings
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        // Required fields
        if (empty($data['patient_name']) || empty($data['lab_name']) || empty($data['lab_item'])) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Patient Name, Lab Name, and Lab Item are required.']);
        }

        // Default status
        if (empty($data['status'])) {
            $data['status'] = 'Sent';
        }

        try {
            $insertID = $this->labOrderModel->insert($data);

            if ($insertID) {
                return $this->response->setStatusCode(201)
                    ->setJSON([
                        'status' => 'success',
                        'message' => 'Lab order placed successfully.',
                        'lab_order_id' => $insertID
                    ]);
            }

            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Failed to place lab order.']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Optional: List lab orders
    public function list()
    {
        $orders = $this->labOrderModel->findAll();
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $orders
        ]);
    }
}

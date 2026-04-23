<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\ProcedureModel;  
use CodeIgniter\Controller;

class InvoiceController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // 🟢 LOAD INVOICE PAGE (VERY IMPORTANT)
    public function index($patient_id, $appointment_id)
{
    // 🔹 Patient info
    $patient = $this->db->table('patients')
        ->where('patient_id', $patient_id)
        ->get()
        ->getRowArray();

    // 🔹 Treatments
    $treatments = $this->db->table('treatments')
        ->where('appointment_id', $appointment_id)
        ->get()
        ->getResultArray();

    // 🔹 Procedures (MISSING BEFORE → THIS FIXES ERROR)
    $procedureModel = new ProcedureModel();
    $procedures = $procedureModel->findAll();

    // 🔹 Invoice ID
    $invoice_id = 'INV-' . rand(1000, 9999);

    return view('patient/invoice', [
        'invoice_id'   => $invoice_id,
        'patient_id'   => $patient['patient_id'],
        'patient_name' => $patient['name'],
        'mr_number'    => $patient['mr_number'],
        'treatments'   => $treatments,

        // ✅ ADD THIS LINE (FIX)
        'procedures'  => $procedures
    ]);
}
    // 🟢 SAVE INVOICE (FORM SUBMIT)
    public function save()
    {
        $data = $this->request->getPost();

        $invoiceModel = new InvoiceModel();

        $invoiceModel->insert([
            'invoice_id'   => $data['invoice_id'],
            'patient_id'   => $data['patient_id'],
            'patient_name' => $data['patient_name'],
            'mr_number'    => $data['mr_number'],
            'description'  => $data['description'],
            'paid_amount'  => $data['paid_amount'],
            'dues'         => $data['dues'],
            'advance'      => $data['advance'],
            'payment_date' => $data['payment_date'],
            'user_name'    => session()->get('username')
        ]);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }
} 
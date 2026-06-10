<?php
namespace App\Controllers;

use App\Models\PatientModel;
use CodeIgniter\Controller;
use App\Models\InvoiceModel;
use App\Models\ProcedureModel;

class PatientProfile extends Controller
{
    public function view($patientId = null)
    {
        if ($patientId === null) {
            return redirect()->to('/adminDashboard');
        }

        $model = new PatientModel();
        $invoiceModel = new InvoiceModel();

        $patient = $model->find($patientId);

        $invoices = $invoiceModel
            ->where('patient_id', $patientId)
            ->orderBy('payment_date', 'DESC')
            ->findAll();

        // ✅ FIX: prevent undefined variable error
        $appointment_id = $this->request->getGet('appointment_id');

        if (!$patient) {
            return redirect()->to('/adminDashboard')->with('error','Patient not found');
        }

        return view('patient/patientprofile', [
            'patient' => $patient,
            'invoices' => $invoices,
            'appointment_id' => $appointment_id
        ]);
    }

    public function invoiceView($patientId)
{
    try {

        $patientModel = new PatientModel();
        $invoiceModel = new InvoiceModel();
        $procedureModel = new ProcedureModel();

        $patient = $patientModel->find($patientId);

        if (!$patient) {
            throw new \Exception("Patient not found");
        }

        // invoice number
        $lastInvoice = $invoiceModel
            ->select('invoice_id')
            ->orderBy('invoice_id','DESC')
            ->first();

        $nextInvoiceId = $lastInvoice
            ? $lastInvoice['invoice_id'] + 1
            : 1001;

        // ALL procedures
        $procedures = $procedureModel
            ->orderBy('procedure_name','ASC')
            ->findAll();

        // ⭐ NEW: GET DOCTOR SELECTED PROCEDURES (IMPORTANT FIX)
        $db = \Config\Database::connect();

        $treatmentRows = $db->table('treatments')
            ->select('procedure_id')
            ->where('patient_id', $patientId)
            ->where('procedure_id IS NOT NULL')
            ->get()
            ->getResultArray();

        $selectedProcedures = array_column($treatmentRows, 'procedure_id');

        return view('patient/invoice', [
            'patient_id'        => $patient['patient_id'],
            'patient_name'      => $patient['name'],
            'mr_number'         => $patient['mr_number'],
            'invoice_id'        => $nextInvoiceId,
            'procedures'        => $procedures,

            // ⭐ ADD THIS
            'selectedProcedures'=> $selectedProcedures
        ]);

    } catch(\Throwable $e){

        log_message('error',$e->getMessage());

        return redirect()->to('/adminDashboard')
            ->with('error','Invoice loading failed');
    }
}

    public function getProcedures()
    {
        try {

            $procedures = $this->procedureModel
                ->orderBy('procedure_name','ASC')
                ->findAll();

            return $this->response->setJSON($procedures);

        } catch(\Throwable $e){

            log_message('error',$e->getMessage());

            return $this->response->setJSON([
                'status'=>'error',
                'error'=>'Unable to load procedures'
            ]);
        }
    }

    public function createProcedure()
    {
        $data = $this->request->getJSON(true);
        $this->procedureModel = new ProcedureModel();

        if (
            empty($data['name']) ||
            empty($data['department']) ||
            !isset($data['price'])
        ) {
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => 'All fields are required'
            ]);
        }

        $insert = [
            'procedure_name' => $data['name'],
            'department'     => $data['department'],
            'price'          => $data['price']
        ];

        $this->procedureModel->insert($insert);

        return $this->response->setJSON([
            'status'    => 'success',
            'procedure' => [
                'procedure_id' => $this->procedureModel->getInsertID(),
                'name'         => $insert['procedure_name'],
                'department'   => $insert['department'],
                'price'        => $insert['price']
            ]
        ]);
    }

    public function updateProcedure()
    {
        try {
            $data = $this->request->getJSON(true);
            $this->procedureModel = new ProcedureModel();

            $map = [
                'name'       => 'procedure_name',
                'department' => 'department',
                'price'      => 'price'
            ];

            if (!isset($map[$data['field']])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'error'  => 'Invalid field'
                ]);
            }

            $this->procedureModel->update($data['id'], [
                $map[$data['field']] => $data['value']
            ]);

            return $this->response->setJSON(['status' => 'success']);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => 'Server error'
            ]);
        }
    }

    public function deleteProcedure()
    {
        try {
            $data = $this->request->getJSON(true);
            $this->procedureModel = new ProcedureModel();

            $this->procedureModel->delete($data['id']);

            return $this->response->setJSON(['status' => 'success']);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => 'Server error'
            ]);
        }
    }

    // public function saveInvoice()
    // {
    //     $invoiceModel = new InvoiceModel();
    //     $data = $this->request->getPost();

    //     $invoiceModel->insert([
    //         'invoice_id'   => $data['invoice_id'],
    //         'mr_number'    => $data['mr_number'],
    //         'patient_id'   => $data['patient_id'],
    //         'patient_name' => $data['patient_name'],
    //         'description'  => $data['description'],
    //         'paid_amount'  => $data['paid_amount'],
    //         'dues'         => $data['dues'],
    //         'advance'      => $data['advance'],
    //         // 'payment_date' => $data['payment_date'],
    //         'payment_date' => date('Y-m-d', strtotime($data['payment_date'])),
    //         'user_name'    => $data['user_name']
    //     ]);

    //     return $this->response->setJSON([
    //         'status'     => 'success',
    //         'invoice_id' => $invoiceModel->getInsertID()
    //     ]);
    // }

    public function saveInvoice()
{
    $invoiceModel = new InvoiceModel();
    $db = \Config\Database::connect();

    $data = $this->request->getPost();

    // 1. SAVE INVOICE
    $invoiceModel->insert([
        'invoice_id'   => $data['invoice_id'],
        'mr_number'    => $data['mr_number'],
        'patient_id'   => $data['patient_id'],
        'patient_name' => $data['patient_name'],
        'description'  => $data['description'],
        'paid_amount'  => $data['paid_amount'],
        'dues'         => $data['dues'],
        'advance'      => $data['advance'],
        'payment_date' => date('Y-m-d', strtotime($data['payment_date'])),
        'user_name'    => $data['user_name']
    ]);

    // 2. GET PROCEDURES FROM TREATMENTS TABLE
    $procedures = $db->table('treatments')
        ->select('procedure_id')
        ->where('patient_id', $data['patient_id'])
        ->where('procedure_id IS NOT NULL')
        ->get()
        ->getResultArray();

    $procedureIds = array_unique(array_column($procedures, 'procedure_id'));

    // 3. DEDUCT INVENTORY
    $this->consumeInventory($procedureIds);

    // 4. RESPONSE
    return $this->response->setJSON([
        'status'     => 'success',
        'invoice_id' => $invoiceModel->getInsertID()
    ]);
}

    private function consumeInventory($procedureIds)
{
    $db = \Config\Database::connect();

    if (empty($procedureIds)) {
        return;
    }

    foreach ($procedureIds as $pid) {

        // get all items linked to procedure
        $items = $db->table('procedure_inventory')
            ->where('procedure_id', $pid)
            ->get()
            ->getResult();

        foreach ($items as $item) {

            // current stock
            $stockRow = $db->table('inventory_items')
                ->where('item_id', $item->item_id)
                ->get()
                ->getRow();

            if (!$stockRow) continue;

            $currentStock = (float)$stockRow->available_qty;
            $usedQty = (float)$item->qty_used;

            // safety check
            if ($currentStock < $usedQty) {
                continue; // skip if not enough stock
            }

            // update stock (reduce)
            $db->table('inventory_items')
                ->where('item_id', $item->item_id)
                ->set('available_qty', 'available_qty - ' . $usedQty, false)
                ->update();

            // log transaction
            $db->table('stock_transactions')->insert([
                'item_id'   => $item->item_id,
                'type'      => 'OUT',
                'quantity'  => $usedQty,
                'note'      => 'Used in procedure ID: ' . $pid,
                'created_at'=> date('Y-m-d H:i:s')
            ]);
        }
    }
}

}
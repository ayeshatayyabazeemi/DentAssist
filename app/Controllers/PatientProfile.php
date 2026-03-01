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
            // invalid request
            return redirect()->to('/adminDashboard'); // or some safe place
        }

        $model = new PatientModel();
            $invoiceModel = new InvoiceModel();

       
  $patient = $model->find($patientId);
         $invoices = $invoiceModel
        ->where('patient_id', $patientId)
        ->orderBy('payment_date', 'DESC')
        ->findAll();


        if (!$patient) {
            // no patient found
            return redirect()->to('/adminDashboard')->with('error','Patient not found');
        }

        // pass data to view or return JSON if you want
        return view('patient/patientprofile', ['patient' => $patient, 'invoices' => $invoices]);
    }
 public function invoiceView($patientId)
{
    try {

        $patientModel = new PatientModel();
        $invoiceModel = new InvoiceModel();
        $procedureModel = new ProcedureModel();

        // Fetch patient
        $patient = $patientModel->find($patientId);

        if (!$patient) {
            throw new \Exception("Patient not found");
        }

        // Generate invoice number
        $lastInvoice = $invoiceModel
            ->select('invoice_id')
            ->orderBy('invoice_id','DESC')
            ->first();

        $nextInvoiceId = $lastInvoice
            ? $lastInvoice['invoice_id'] + 1
            : 1001;

        // Load procedures
        $procedures = $procedureModel
            ->orderBy('procedure_name','ASC')
            ->findAll();

        return view('patient/invoice',[
            'patient_id' => $patient['patient_id'],
            'patient_name' => $patient['name'],
            'mr_number' => $patient['mr_number'],
            'invoice_id' => $nextInvoiceId,
            'procedures' => $procedures
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

    /* =========================================================
       CREATE PROCEDURE
       JS → { name, department, price }
    ========================================================= */
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
        log_message('info', 'UpdateProcedure payload: ' . json_encode($data));

        if (
            empty($data['id']) ||
            empty($data['field']) ||
            !array_key_exists('value', $data)
        ) {
            log_message('error', 'UpdateProcedure: Invalid payload');
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => 'Invalid update request'
            ]);
        }

        $map = [
            'name'       => 'procedure_name',
            'department' => 'department',
            'price'      => 'price'
        ];

        if (!isset($map[$data['field']])) {
            log_message('error', 'UpdateProcedure: Invalid field ' . $data['field']);
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => 'Invalid field'
            ]);
        }

        // Check if record exists
        $procedure = $this->procedureModel->find($data['id']);
        if (!$procedure) {
            log_message('error', 'UpdateProcedure: Procedure not found ID=' . $data['id']);
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => 'Procedure not found'
            ]);
        }

        $this->procedureModel->update($data['id'], [
            $map[$data['field']] => $data['value']
        ]);

        log_message(
            'info',
            "Procedure updated | ID={$data['id']} | Field={$map[$data['field']]} | Value={$data['value']}"
        );

        return $this->response->setJSON(['status' => 'success']);

    } catch (\Throwable $e) {
        log_message('critical', 'UpdateProcedure exception: ' . $e->getMessage());

        return $this->response->setJSON([
            'status' => 'error',
            'error'  => 'Server error while updating'
        ]);
    }
}
    public function deleteProcedure()
{
    try {
        $data = $this->request->getJSON(true);
     $this->procedureModel = new ProcedureModel();
        log_message('info', 'DeleteProcedure payload: ' . json_encode($data));

        if (empty($data['id'])) {
            log_message('error', 'DeleteProcedure: Missing ID');
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => 'Invalid ID'
            ]);
        }

        // Check if procedure exists
        $procedure = $this->procedureModel->find($data['id']);
        if (!$procedure) {
            log_message('error', 'DeleteProcedure: Procedure not found ID=' . $data['id']);
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => 'Procedure not found'
            ]);
        }

        $this->procedureModel->delete($data['id']);

        log_message('info', 'Procedure deleted ID=' . $data['id']);

        return $this->response->setJSON(['status' => 'success']);

    } catch (\Throwable $e) {
        log_message('critical', 'DeleteProcedure exception: ' . $e->getMessage());

        return $this->response->setJSON([
            'status' => 'error',
            'error'  => 'Server error while deleting'
        ]);
    }
}

    /* =========================================================
       INSERT INVOICE (AJAX SAFE)
    ========================================================= */
    public function saveInvoice()
    {
        $invoiceModel = new InvoiceModel();
        $data = $this->request->getPost();

        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => 'Invalid request'
            ]);
        }

        $invoiceModel->insert([
            'invoice_id'   => $data['invoice_id'],
            'mr_number'    => $data['mr_number'],
            'patient_id'   => $data['patient_id'],
            'patient_name' => $data['patient_name'],
            'description'  => $data['description'],
            'paid_amount'  => $data['paid_amount'],
            'dues'         => $data['dues'],
            'advance'      => $data['advance'],
            'payment_date' => $data['payment_date'],
            'user_name'    => $data['user_name']
        ]);

        return $this->response->setJSON([
            'status'     => 'success',
            'invoice_id' => $invoiceModel->getInsertID()
        ]);
    }

}
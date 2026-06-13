<?php
namespace App\Controllers;

use App\Models\PatientModel;
use CodeIgniter\Controller;
use App\Models\InvoiceModel;
use App\Models\ProcedureModel;
use App\Models\AppointmentModel;

class PatientProfile extends Controller
{




// public function paretoPatients()
// {
//     try {

//         log_message('info', 'paretoPatients API called');

//         $invoiceModel = new InvoiceModel();

//         // ===============================
//         // Fetch patient revenue + visits
//         // ===============================
//         $patients = $invoiceModel
//             ->select("
//                 patients.patient_id,
//                 patients.name AS patient_name,
//                 SUM(invoice.paid_amount) AS revenue,
//                 COUNT(invoice.invoice_id) AS visits
//             ")
//             ->join('patients', 'patients.patient_id = invoice.patient_id')
//             ->where('invoice.paid_amount >', 0)
//             ->groupBy('patients.patient_id, patients.name')
//             ->orderBy('revenue', 'DESC')
//             ->findAll();

//         // ===============================
//         // Filter loyal patients (visits >= 7)
//         // ===============================
//         $loyalPatients = array_values(array_filter($patients, function($p){
//             return $p['visits'] >= 9;
//         }));

//         // ===============================
//         // Calculate totals
//         // ===============================
//         $totalPatients = count($loyalPatients);

//         if ($totalPatients == 0) {
//             return $this->response->setJSON([
//                 'status' => 'success',
//                 'data' => [],
//                 'insight' => 'No loyal patients found.'
//             ]);
//         }

//         // Top 20% of loyal patients
//         $topCount = ceil($totalPatients * 0.2);

//         $vipPatients = array_slice($loyalPatients, 0, $topCount);

//         // ===============================
//         // Chart data (Top 10)
//         // ===============================
//         $chartPatients = array_slice($loyalPatients, 0, 10);

//         $labels = [];
//         $revenues = [];

//         foreach ($chartPatients as $p) {
//             $labels[] = $p['patient_name'];
//             $revenues[] = (float)$p['revenue'];
//         }

//        // ===============================
// // Insight calculation (all patients)
// // ===============================
// $allPatients = $invoiceModel
//     ->select("
//         patients.patient_id,
//         patients.name AS patient_name,
//         SUM(invoice.paid_amount) AS revenue
//     ")
//     ->join('patients', 'patients.patient_id = invoice.patient_id')
//     ->where('invoice.paid_amount >', 0)
//     ->groupBy('patients.patient_id, patients.name')
//     ->orderBy('revenue', 'DESC')
//     ->findAll();

// $totalAllRevenue = array_sum(array_column($allPatients, 'revenue'));
// $topCountAll = ceil(count($allPatients) * 0.2);
// $topRevenuePatients = array_slice($allPatients, 0, $topCountAll);
// $vipRevenueAll = array_sum(array_column($topRevenuePatients, 'revenue'));

// $percentageAll = $totalAllRevenue > 0 
//     ? round(($vipRevenueAll / $totalAllRevenue) * 100, 2) 
//     : 0;

// $insight = "Top 20% of all patients generate {$percentageAll}% of clinic revenue.";
//         return $this->response->setJSON([
//             'status' => 'success',

//             'data' => [
//                 'chart' => [
//                     'labels' => $labels,
//                     'revenues' => $revenues
//                 ],
//                 'vip_patients' => $vipPatients
//             ],

//             'stats' => [
//                 'total_loyal_patients' => $totalPatients,
//                 'vip_count' => $topCount,
//                 'vip_revenue_percent' => $percentageAll
//             ],

//             'insight' => $insight
//         ]);

//     } catch (\Exception $e) {

//         log_message('error', 'Error in paretoPatients: ' . $e->getMessage());

//         return $this->response->setJSON([
//             'status' => 'fail',
//             'message' => 'Something went wrong'
//         ]);
//     }
// }

public function paretoPatients()
{
    try {

        log_message('info', 'paretoPatients API called');

        $invoiceModel = new InvoiceModel();

        // ===============================
        // Fetch patient revenue + visits
        // ===============================
        $patients = $invoiceModel
            ->select("
                patients.patient_id,
                patients.name AS patient_name,
                SUM(invoice.paid_amount) AS revenue,
                COUNT(invoice.invoice_id) AS visits
            ")
            ->join('patients', 'patients.patient_id = invoice.patient_id')
            ->where('invoice.paid_amount >', 0)
            ->groupBy('patients.patient_id, patients.name')
            ->orderBy('revenue', 'DESC')
            ->findAll();

        // ===============================
        // Filter loyal patients (visits >= 9)
        // ===============================
        $loyalPatients = array_values(array_filter($patients, function($p){
            return $p['visits'] >= 9;
        }));

        // ===============================
        // Calculate totals
        // ===============================
        $totalPatients = count($loyalPatients);

        if ($totalPatients == 0) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [],
                'insight' => 'No loyal patients found.'
            ]);
        }

        // Top 20% of loyal patients
        $topCount = ceil($totalPatients * 0.2);

        $vipPatients = array_slice($loyalPatients, 0, $topCount);

        // Format VIP patients list for JSON
        $vipPatientsList = [];
        foreach ($vipPatients as $p) {
            $vipPatientsList[] = [
                'patient_id'   => $p['patient_id'],
                'patient_name' => $p['patient_name'],
                'revenue'      => number_format((float)$p['revenue'], 2, '.', ''),
                'visits'       => (int)$p['visits']
            ];
        }

        // ===============================
        // Chart data (Top 10 loyal patients)
        // ===============================
        $chartPatients = array_slice($loyalPatients, 0, 10);

        $labels = [];
        $revenues = [];
        foreach ($chartPatients as $p) {
            $labels[] = $p['patient_name'];
            $revenues[] = (float)$p['revenue'];
        }

        // ===============================
        // Insight calculation (all patients)
        // ===============================
        $allPatients = $invoiceModel
            ->select("
                patients.patient_id,
                patients.name AS patient_name,
                SUM(invoice.paid_amount) AS revenue
            ")
            ->join('patients', 'patients.patient_id = invoice.patient_id')
            ->where('invoice.paid_amount >', 0)
            ->groupBy('patients.patient_id, patients.name')
            ->orderBy('revenue', 'DESC')
            ->findAll();

        $totalAllRevenue = array_sum(array_column($allPatients, 'revenue'));
        $topCountAll = ceil(count($allPatients) * 0.2);
        $topRevenuePatients = array_slice($allPatients, 0, $topCountAll);
        $vipRevenueAll = array_sum(array_column($topRevenuePatients, 'revenue'));

        $percentageAll = $totalAllRevenue > 0 
            ? round(($vipRevenueAll / $totalAllRevenue) * 100, 2) 
            : 0;

        $insight = "Top 20% of all patients generate {$percentageAll}% of clinic revenue.";

        // ===============================
        // Return JSON
        // ===============================
        return $this->response->setJSON([
    'status' => 'success',

    'data' => [
        'chart' => [
            'labels' => $labels,
            'revenues' => $revenues
        ],

        // VIP patients (top 20% of loyal)
        'vip_patients' => $vipPatients,

        // FULL loyal patient list
        'loyal_patients' => $loyalPatients
    ],

    'stats' => [
        'total_loyal_patients' => $totalPatients,
        'vip_count' => $topCount,
        'vip_revenue_percent' => $percentageAll
    ],

    'insight' => $insight
]);

    } catch (\Exception $e) {

        log_message('error', 'Error in paretoPatients: ' . $e->getMessage());

        return $this->response->setJSON([
            'status' => 'fail',
            'message' => 'Something went wrong'
        ]);
    }
}



 public function list()
    {
        try {
            $procedureModel = new ProcedureModel();

            // Fetch all procedures
            $procedures = $procedureModel
                ->select('procedure_name, price')
                ->orderBy('procedure_name', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $procedures
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

public function getProcedureRevenueSummary()
{
    try {

        log_message('info', 'getProcedureRevenueSummary API called');

        $invoiceModel = new InvoiceModel();

        // ===============================
        // Fetch all invoices
        // ===============================
        $invoices = $invoiceModel
            ->select("invoice_id, paid_amount, description")
            ->where("paid_amount >", 0)
            ->findAll();

        $procedureRevenue = [];
        $procedureCount   = [];

        foreach ($invoices as $inv) {

            $desc = $inv['description'];

            if (!$desc) {
                continue;
            }

            // ===============================
            // Split procedures (handle multiple separators)
            // ===============================
            $procedures = preg_split('/[,;+|]/', $desc);
            $procedures = array_filter(array_map('trim', $procedures));

            $procCount = count($procedures);

            if ($procCount === 0) {
                continue;
            }

            // Revenue share per procedure
            $share = $inv['paid_amount'] / $procCount;

            foreach ($procedures as $proc) {

                $proc = strtolower(trim($proc));

                // ===============================
                // Normalize procedure names
                // ===============================
                if (str_starts_with($proc, 'consult')) {
                    $procName = 'Consultation';
                }
                elseif (str_starts_with($proc, 'scal')) {
                    $procName = 'Scaling';
                }
                elseif (str_starts_with($proc, 'root canal')) {
                    $procName = 'Root Canal Treatment';
                }
                elseif (str_starts_with($proc, 'opg')) {
                    $procName = 'OPG';
                }
                elseif (str_starts_with($proc, 'extract')) {
                    $procName = 'Extraction';
                }
                elseif (str_starts_with($proc, 'composite')) {
                    $procName = 'Composite Filling';
                }
                elseif (str_starts_with($proc, 'polish')) {
                    $procName = 'Polishing';
                }
                elseif (str_starts_with($proc, 'ortho')) {
                    $procName = 'Orthodontics';
                }
                else {
                    $procName = ucfirst($proc);
                }

                // ===============================
                // Initialize if not exists
                // ===============================
                if (!isset($procedureRevenue[$procName])) {
                    $procedureRevenue[$procName] = 0;
                    $procedureCount[$procName]   = 0;
                }

                // ===============================
                // Aggregate revenue + count
                // ===============================
                $procedureRevenue[$procName] += $share;
                $procedureCount[$procName] += 1;
            }
        }

        // ===============================
        // Calculate total revenue
        // ===============================
        $totalRevenue = array_sum($procedureRevenue);

        // ===============================
        // Prepare final result
        // ===============================
        $result = [];

        foreach ($procedureRevenue as $name => $rev) {

            $result[] = [
                'procedure_name' => $name,
                'total_revenue'  => round($rev, 2),
                'count'          => $procedureCount[$name],
                'percentage'     => round(($rev / max($totalRevenue, 1)) * 100, 2)
            ];
        }

        // ===============================
        // Sort descending by revenue
        // ===============================
        usort($result, function ($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $result
        ]);

    } catch (\Exception $e) {

        log_message('error', 'Error in getProcedureRevenueSummary: ' . $e->getMessage());

        return $this->response->setJSON([
            'status'  => 'fail',
            'message' => 'Something went wrong'
        ]);
    }
}


public function getInvoiceSummary()
{
    try {

        log_message('info', 'getInvoiceSummary API called');

        $invoiceModel = new InvoiceModel();

        // ===============================
        // Fetch all invoices
        // ===============================
        $invoices = $invoiceModel
            ->select("
                DATE_FORMAT(payment_date_new,'%Y-%m-01') as month,
                SUM(paid_amount) as total_revenue,
                COUNT(DISTINCT patient_id) as patient_count,
                GROUP_CONCAT(description SEPARATOR '|') as all_descriptions
            ")
            ->where("payment_date_new IS NOT NULL")
            ->where("YEAR(payment_date_new) > 2010") // safety
            ->groupBy("YEAR(payment_date_new), MONTH(payment_date_new)")
            ->orderBy("month","ASC")
            ->findAll();

        // ===============================
        // Compute total procedures per month
        // ===============================
        foreach ($invoices as &$inv) {

            $desc = $inv['all_descriptions'];

            if($desc){
                // Split by ',' or '+' or ' + ' to count individual procedures
                $procedures = preg_split('/,|\+/', $desc);
                $inv['procedures'] = count(array_filter(array_map('trim', $procedures)));
            } else {
                $inv['procedures'] = 0;
            }

            // Remove helper column
            unset($inv['all_descriptions']);
        }

        // Log response data
        log_message('info', 'Invoice Summary Response: ' . json_encode($invoices));

        return $this->response->setJSON($invoices);

    } catch (\Exception $e) {

        log_message('error', 'Invoice Summary Error: ' . $e->getMessage());

        return $this->response->setJSON([
            "error" => $e->getMessage()
        ]);
    }
}

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

    public function saveInvoice()
    {
        $invoiceModel = new InvoiceModel();
        $data = $this->request->getPost();

        $invoiceModel->insert([
            'invoice_id'   => $data['invoice_id'],
            'mr_number'    => $data['mr_number'],
            'patient_id'   => $data['patient_id'],
            'patient_name' => $data['patient_name'],
            'description'  => $data['description'],
            'paid_amount'  => $data['paid_amount'],
            'dues'         => $data['dues'],
            'advance'      => $data['advance'],
            // 'payment_date' => $data['payment_date'],
            'payment_date' => date('Y-m-d', strtotime($data['payment_date'])),
            'user_name'    => $data['user_name']
        ]);

        return $this->response->setJSON([
            'status'     => 'success',
            'invoice_id' => $invoiceModel->getInsertID()
        ]);
    }
}
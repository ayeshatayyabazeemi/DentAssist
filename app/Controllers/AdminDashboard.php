<?php
namespace App\Controllers;
use App\Models\PatientModel;


class AdminDashboard extends BaseController
{
    public function index()
    {
       
        // Only allow if logged in
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        helper('url');
       
        $model = new PatientModel();
    $raw = $model->getMonthlyRegistrationsRaw(24);

// Build labels + data
$chartLabels = [];
$chartData   = [];

// Map raw results by year-month for easy lookup
$temp = [];
foreach ($raw as $row) {
    $ym = $row['reg_year'] . '-' . str_pad($row['reg_month'], 2, '0', STR_PAD_LEFT);
    $temp[$ym] = (int) $row['registrations'];
}
$months = 24; // last 24 months
$thresholdDate = date('Y-m-d', strtotime("-{$months} months"));


// Now generate the last 24 months list
for ($i = $months - 1; $i >= 0; $i--) {
    $dt = new \DateTime();
    $dt->modify("-{$i} months");
    $ym = $dt->format('Y-m');

    $chartLabels[] = $ym;
    $chartData[] = isset($temp[$ym]) ? $temp[$ym] : 0;
}
    $totalPatients = $model->getTotalPatients();
    $last12        = $model->getRegistrationsLast12Months();
    $prev12        = $model->getRegistrationsPrev12Months();

 


    $growthPercent = ($prev12 > 0)
        ? round((($last12 - $prev12) / $prev12) * 100, 1)
        : null;

    $data = [
       
        'totalPatients' => $totalPatients,
        'last12'        => $last12,
        'growthPercent' => $growthPercent,
         'chartLabels' => $chartLabels,
    'chartData'   => $chartData,
    ];

    log_message('debug', 'Dashboard view data: ' . print_r($data, true));

    return view('admin/dashboard', $data);

        // load your dashboard view
       
    }
}

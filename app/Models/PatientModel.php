<?php
namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';            // your patients table
    protected $primaryKey = 'patient_id';     // numeric auto_increment id

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'name',
        'mobile_no',
        'email',
        'gender',
        'age',
        'address',
        'occupation',
        'regdate',
        'guardianname',
        'guardianphonenumber',
        'guardianrelation',
        'referredBy',
        'doctorName',
        'cnic',
        'bloodGroup',
        'insurance',
        'mr_number',
    ];

    protected $validationRules = [
        'name'  => 'required|min_length[2]|max_length[50]',
        'email' => 'permit_empty|valid_email',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

     /** 
     * Get monthly registrations for the last N months.
     * @param int $months Number of months to go back (e.g. 24)
     * @return array
     */



     protected $afterFind = ['formatMobile'];

protected function formatMobile(array $data)
{
    if (isset($data['data'])) {
        // Single row
        if (isset($data['data']['mobile_no'])) {
            if (strpos($data['data']['mobile_no'], '0') !== 0) {
                $data['data']['mobile_no'] = '0' . $data['data']['mobile_no'];
            }
        }

        // Multiple rows
        if (is_array($data['data'])) {
            foreach ($data['data'] as &$row) {
                if (isset($row['mobile_no']) && strpos($row['mobile_no'], '0') !== 0) {
                    $row['mobile_no'] = '0' . $row['mobile_no'];
                }
            }
        }
    }
    return $data;
}

public function getMonthlyRegistrationsRaw(int $months = 24): array
{
    $db = \Config\Database::connect();

    // Raw SQL: get year, month, count
    $sql = "
      SELECT 
        YEAR(regdate) AS reg_year, 
        MONTH(regdate) AS reg_month,
        COUNT(*) AS registrations
      FROM `" . $this->table . "`
      WHERE regdate > ? 
      GROUP BY YEAR(regdate), MONTH(regdate)
      ORDER BY YEAR(regdate), MONTH(regdate)
    ";

    // Calculate threshold date
    $thresholdDate = date('Y-m-d', strtotime("-{$months} months"));

    $query = $db->query($sql, [$thresholdDate]);
    $results = $query->getResultArray();  // CI4: getResultArray returns array of assoc arrays :contentReference[oaicite:0]{index=0}

    return $results;
}






    /**
     * Get total number of patients (all-time).
     * @return int
     */
    public function getTotalPatients(): int
    {
        $total = $this->countAll();
        log_message('debug', 'Total patients count', ['total' => $total]);
        return $total;
    }

    /**
     * Get number of registrations in the last 12 months.
     * @return int
     */
    public function getRegistrationsLast12Months(): int
    {
        $builder = $this->builder();
        $start = date('Y-m-d', strtotime('-12 months'));
        $end = date('Y-m-d');

        log_message('debug', 'Getting last 12 months registrations', [
            'start' => $start,
            'end' => $end,
        ]);

        $builder->selectCount('*', 'count')
            ->where('regdate >=', $start)
            ->where('regdate <=', $end);

        $row = $builder->get()->getRowArray();
        $count = (int) ($row['count'] ?? 0);

        log_message('debug', 'Last12 count', ['count' => $count]);
        return $count;
    }

    /**
     * Get number of registrations in the 12‑24 month window (previous year).
     * @return int
     */
    public function getRegistrationsPrev12Months(): int
    {
        $builder = $this->builder();
        $startPrev = date('Y-m-d', strtotime('-24 months'));
        $endPrev = date('Y-m-d', strtotime('-12 months'));

        log_message('debug', 'Getting registrations for 12‑24 months ago', [
            'startPrev' => $startPrev,
            'endPrev' => $endPrev,
        ]);

        $builder->selectCount('*', 'count')
            ->where('regdate >=', $startPrev)
            ->where('regdate <', $endPrev);

        $row = $builder->get()->getRowArray();
        $count = (int) ($row['count'] ?? 0);

        log_message('debug', 'Prev12 count', ['count' => $count]);
        return $count;
    }
}
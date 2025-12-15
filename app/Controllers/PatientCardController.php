<?php
namespace App\Controllers;

use App\Models\PatientModel;
use App\Models\AppointmentModel;
use App\Models\EmployeeModel; // For doctor names
use CodeIgniter\Controller;
use TCPDF;

class PatientCardController extends Controller
{
    public function generate($patientId = null)
    {
        if ($patientId === null) {
            return redirect()->to('/adminDashboard');
        }

        $patientModel = new PatientModel();
        $appointmentModel = new AppointmentModel();
        $employeeModel = new EmployeeModel();

        $patient = $patientModel->find($patientId);

        if (!$patient) {
            return redirect()->to('/adminDashboard')->with('error','Patient not found');
        }

        // Fetch all appointments for this patient
        $appointments = $appointmentModel
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->findAll();

        // Clear output buffer
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        require_once ROOTPATH . 'vendor/autoload.php';

        // A7 ID card: 74mm x 104mm
        $pdf = new TCPDF('P', 'mm', [74, 104]);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();

        // ===== Card Border =====
        $pdf->SetLineWidth(0.3);
        $pdf->Rect(2, 2, 70, 100, 'D');

        // ===== Hospital Logo (Top Center) =====
        $logoPath = FCPATH . 'assets/images/smile.jpg';
        if (file_exists($logoPath)) {
            $logoWidth = 18;
            $xLogo = ($pdf->getPageWidth() - $logoWidth) / 2;
            $pdf->Image($logoPath, $xLogo, 4, $logoWidth, 0, '', '', 'T', false, 300);
        }

        // ===== Gender photo (Right) =====
        $gender = strtolower($patient['gender'] ?? '');
        $photoPath = '';
        if ($gender === 'male') {
            $photoPath = FCPATH . 'assets/images/male.jpeg';
        } elseif ($gender === 'female') {
            $photoPath = FCPATH . 'assets/images/femalee.jpeg';
        }
        if ($photoPath && file_exists($photoPath)) {
            $pdf->Image($photoPath, 50, 25, 20, 20, '', '', '', false, 300);
        }

        // ===== Patient Info =====
        $pdf->SetXY(5, 25);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(0, 0, 80);
        $pdf->Cell(0, 4, 'Patient Card', 0, 1, 'L');

        $mrNumber = $patient['mr_number'] ?? '';
        $mrNumberDigits = preg_replace('/\D/', '', $mrNumber);

        $labels = ['Name', 'Gender', 'Phone', 'Address', 'MR Number'];
        $values = [
            $patient['name'],
            ucfirst($patient['gender']),
            $patient['mobile_no'],
            $patient['address'] ?? '',
            $mrNumberDigits
        ];

        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetTextColor(0, 0, 0);
        $lineHeight = 3;
        $labelWidth = 17;
        $valueWidth = 26;

        foreach ($labels as $i => $label) {
            $pdf->Cell($labelWidth, $lineHeight, $label . ':', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 7);
            $pdf->Cell($valueWidth, $lineHeight, $values[$i], 0, 1, 'L');
            $pdf->SetFont('helvetica', 'B', 7);
        }

        // ===== QR Code (centered below patient info) =====
        $qrSize = 30;
        $y = $pdf->GetY() + 3;
        $x = ($pdf->getPageWidth() - $qrSize) / 2;

        // QR content: patient info + all appointments
        $qrContent = "Name: {$patient['name']}\n";
        $qrContent .= "Gender: " . ucfirst($patient['gender']) . "\n";
        $qrContent .= "Phone: {$patient['mobile_no']}\n";
        $qrContent .= "Address: " . ($patient['address'] ?? '') . "\n";
        $qrContent .= "MR Number: " . $mrNumberDigits;

        if (!empty($appointments)) {
            $qrContent .= "\nAppointments:";
            foreach ($appointments as $appointment) {
                $doctor = $employeeModel->find($appointment['employee_id']);
                $doctorName = $doctor['name'] ?? 'Unknown';
                $apptDate = date('d-m-Y', strtotime($appointment['appointment_date']));
                $apptTime = date('h:i A', strtotime($appointment['appointment_time']));
                $qrContent .= "\nDoctor: $doctorName | Date: $apptDate | Time: $apptTime";
            }
        }

        $style = [
            'border' => 0,
            'padding' => 1,
            'fgcolor' => [0,0,0],
            'bgcolor' => false
        ];

        $pdf->write2DBarcode(
            $qrContent,
            'QRCODE,H',
            $x,
            $y,
            $qrSize,
            $qrSize,
            $style,
            'N'
        );

        // Output PDF - Force download instead of inline
        $pdf->Output('PatientCard_'.$patientId.'.pdf', 'D');
        exit;
    }
}

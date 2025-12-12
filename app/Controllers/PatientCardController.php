<?php
namespace App\Controllers;

use App\Models\PatientModel;
use CodeIgniter\Controller;
use TCPDF;

class PatientCardController extends Controller
{
    public function generate($patientId = null)
    {
        if ($patientId === null) {
            return redirect()->to('/adminDashboard');
        }

        $model = new PatientModel();
        $patient = $model->find($patientId);

        if (!$patient) {
            return redirect()->to('/adminDashboard')->with('error','Patient not found');
        }

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

        // ===== Patient Info (tight layout) =====
        $pdf->SetXY(5, 25);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(0, 0, 80);
        $pdf->Cell(0, 4, 'Patient Card', 0, 1, 'L');

        // Keep MR Number digits only
        $mrNumber = $patient['mr_number'] ?? '';
        $mrNumberDigits = preg_replace('/\D/', '', $mrNumber);

        $labels = ['Name', 'ID', 'Age', 'Gender', 'Phone', 'Address', 'MR Number'];
        $values = [
            $patient['name'],
            $patient['patient_id'],
            $patient['age'],
            ucfirst($patient['gender']),
            $patient['mobile_no'],
            $patient['address'] ?? '',
            $mrNumberDigits
        ];

        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetTextColor(0, 0, 0);
        $lineHeight = 3; // slightly tighter
        $labelWidth = 17;
        $valueWidth = 26;

        foreach ($labels as $i => $label) {
            $pdf->Cell($labelWidth, $lineHeight, $label . ':', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 7);
            $pdf->Cell($valueWidth, $lineHeight, $values[$i], 0, 1, 'L');
            $pdf->SetFont('helvetica', 'B', 7);
        }

        // ===== Barcode (centered below info) =====
        $style = ['border'=>1,'padding'=>2,'fgcolor'=>[0,0,0]];
        $barcodeWidth = 45;
        $barcodeHeight = 12;
        $x = ($pdf->getPageWidth() - $barcodeWidth) / 2;
        $y = 58;

        // Line above barcode
        $pdf->SetLineWidth(0.1);
        $pdf->Line($x, $y - 2, $x + $barcodeWidth, $y - 2);

        // Write barcode
        $pdf->write1DBarcode(
            $patient['patient_id'],
            'C128',
            $x,
            $y,
            $barcodeWidth,
            $barcodeHeight,
            0.4,
            $style,
            'N'
        );

        // Output PDF
        $pdf->Output('PatientCard_'.$patientId.'.pdf', 'I');
        exit;
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 23da279fd58c9be3a184acfff278526b71769919

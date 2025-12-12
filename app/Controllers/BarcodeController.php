<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use TCPDF;

class BarcodeController extends Controller
{
    public function test()
    {
        // Disable CI4 output buffering
        service('response')->setHeader('Content-Type', 'application/pdf');

        // Clear ALL output buffers
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Load TCPDF
        require_once ROOTPATH . 'vendor/autoload.php';

        $pdf = new TCPDF();

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'TCPDF Barcode Test', 0, 1, 'C');

        $style = [
            'border' => true,
            'padding' => 4,
            'fgcolor' => [0, 0, 0],
        ];

        $pdf->write1DBarcode('123456789012', 'C128', 40, 50, '', 40, 0.4, $style, 'N');

        // Output PDF directly (no CI output, otherwise error!)
        $pdf->Output('barcode_test.pdf', 'I');
        exit; // STOP CI from adding extra output
    }
}

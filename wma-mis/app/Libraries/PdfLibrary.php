<?php

namespace App\Libraries;

use Mpdf\Mpdf;

class PdfLibrary
{
    public function renderPdf(string $orientation , string $view, array $data, string $title)
    {
        $commonTask = new CommonTasksLibrary();
        $data['center'] = $commonTask->getCenterAddress();
        $year = date('Y');
        $pdf = new Mpdf(['orientation' => $orientation]);
        $pdf->SetWatermarkImage('assets/images/watermark.png', 0.9, [100, 100]);
        $footer = "<p style='text-align: center;'>Weights and Measures Agency &copy; $year </p>";
     
        
        $pdf->SetHTMLFooter($footer);
        $pdf->showWatermarkImage = true;
        $pdf->WriteHTML(view($view, $data));
        $pdf->Output($title . numString(5) . '.pdf', 'I');
        exit;
    }


    


    
}

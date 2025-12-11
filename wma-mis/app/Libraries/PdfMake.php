<?php


namespace App\Libraries;

use Mpdf\Mpdf;

class PdfMake
{
    public function renderPdf(string $orientation, $source, array $data, string $title)
    {
        $commonTask = new CommonTasksLibrary();
        $data['center'] = $commonTask->getCenterAddress();
        $year = date('Y');
        $pdf = new Mpdf(['orientation' => $orientation]);
        $pdf->SetWatermarkImage('assets/images/watermark.png', 0.9, [100, 100]);
        $footer = "<p style='text-align: center;'>Weights and Measures Agency &copy; $year </p>";

        $pdf->SetHTMLFooter($footer);
        $pdf->showWatermarkImage = true;

        // Generate HTML table
        $htmlChunks = $this->generateHtmlTableChunks($source);

        // Write HTML chunks to PDF
        foreach ($htmlChunks as $chunk) {
            $pdf->WriteHTML($chunk);
        }

        $pdf->Output($title . numString(5) . '.pdf', 'D');
    }

    // Function to generate HTML table from array of objects and split into smaller chunks
    private function generateHtmlTableChunks(array $sales): array
    {

        // $html .= '</tbody></table>';
        $html = view('ReportTemplates/importedPdf', ['imported' => $sales, 'title' => 'Imported', 'center' => wmaCenter()]);


        // Split HTML into smaller chunks
        $chunkSize = 50000; // Set according to your needs
        $chunks = str_split($html, $chunkSize);
        return $chunks;
    }
}

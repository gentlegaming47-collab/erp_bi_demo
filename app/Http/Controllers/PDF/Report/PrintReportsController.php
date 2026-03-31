<?php

namespace App\Http\Controllers\PDF\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PDF\PrintSalesOrderController;
use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 

class PrintReportsController extends Controller
{
   public function mergeReports(Request $request){

        $pdf = new FPDI();

        $report_data = json_decode($request['report_data']);

    foreach ($report_data as $data) {

        // Get the PDF content using the previewRTReport method
            $rtreport = new PrintSalesOrderController();
            $getRTReport = $rtreport->printSalesOrder($data,'yes'); // This should return the PDF content

            // Validate the returned content
            if (stripos($getRTReport, '%PDF-') === false) {
               // Log::error("Invalid PDF content for report $number");
                //throw new \Exception("Invalid PDF content for report $number");
            }

            // Create a temporary file path for this report
            $tempFilePath = public_path("storage/temp_merge_pdf/temp_report_$data.pdf");

            // Save PDF content to a temporary file
            if (file_put_contents($tempFilePath, $getRTReport) === false) {
                //Log::error("Failed to write temporary PDF file for report $number");
                //throw new \Exception("Temporary PDF file could not be created for report $number");
            }

            // Check if the temporary file was created successfully
            if (!file_exists($tempFilePath)) {
               // Log::error("Temporary PDF file does not exist for report $number");
                // throw new \Exception("Temporary PDF file does not exist for report $number");
            }

            // Set the source file from the temporary file
            $pageCount = $pdf->setSourceFile($tempFilePath); // Use the temporary file

            // Loop through each page and add it to the merged PDF
            for ($i = 1; $i <= $pageCount; $i++) {
                $templateId = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($templateId);
            }

            // Optionally delete the temporary file after use
            unlink($tempFilePath);
        }

      
            // Generate a unique file name
            $uniqueId = str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);
            $outputPath = "temp_merge_pdf/SALES_ORDER_{$uniqueId}.pdf"; // Relative path inside the storage

            // Ensure the directory exists
            Storage::makeDirectory('temp_merge_pdf');

            // Save the PDF to the file path
            $fullOutputPath = storage_path("app/public/{$outputPath}"); // Full server path to storage
            $pdf->Output($fullOutputPath, 'F'); // Save the PDF to the file path

            // Return the publicly accessible URL
            return response()->json([
                'response_code'    => '1',
                'outputPath'       => asset("storage/{$outputPath}"), // Public URL
                'response_message' => '',
            ]);
    }

    public function downloadSingleMptReports(Request $request)
    {
        $report_data = json_decode($request['report_data']);
        $downloadLinks = [];

        foreach ($report_data as $data) {
            $name = str_replace("/", "_", $data->name);
            $filePath = storage_path('app/public/reports/' . $data->type . '_reports_file/' . $name . '.pdf');
           // $filePath = public_path('reports/' . $data->type . '_reports_file/' . $name . '.pdf');

            // Check if file exists, otherwise generate it
            if (!file_exists($filePath)) {
                GeneratePdf(base64_decode($data->id), $name, $data->type, 'yes','listing');
                
            }

            if (file_exists($filePath)) {
                $downloadLinks[] = [
                    'name' => $name . '.pdf',
                    'url'  => asset('storage/reports/' . $data->type . '_reports_file/' . $name . '.pdf')
                    //'url'  => asset('reports/' . $data->type . '_reports_file/' . $name . '.pdf')
                ];
            }
        }

        return response()->json(['files' => $downloadLinks]);
    }
}
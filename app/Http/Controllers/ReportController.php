<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ReportTemplate;
use App\Models\TargetReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Traits\DocFileUpload;
use Barryvdh\DomPDF\Facade\Pdf;


class ReportController extends Controller
{

    use DocFileUpload;

    /**
     * Display a listing of Report Template.
     *
     * @return \Illuminate\Http\Response
     */

     public function show()
    {
        try {
            $scheduleSession = ReportTemplate::first();
            return response()->json($scheduleSession, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not found report template.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateReportTemplate(Request $request)
    {
        $validated = $request->validate([
            'templateId' => 'nullable|integer',
            'template' => 'required',
        ]);
    
        $session = ReportTemplate::updateOrCreate(
            ['id' => $validated['templateId']],
            ['template' => $validated['template']]
        );
    
        return response()->json(['success' => 'Report template updated']);
    }

    public function uploadDocx(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|mimes:docx',
            'student_id' => 'required|exists:students,id',
        ]);

        try {
           

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName);

            $extractTo = storage_path('app/uploads/' . pathinfo($fileName, PATHINFO_FILENAME));

        
            $zip = new \ZipArchive;
            if ($zip->open(storage_path('app/' . $filePath)) === TRUE) {
                $zip->extractTo($extractTo);
                $zip->close();
            } else {
                return back()->withErrors('Failed to open the DocX file.');
            }

            $xmlFile = $extractTo . '/word/document.xml';
            if (!file_exists($xmlFile)) {
                return back()->withErrors('Failed to find document.xml.');
            }

            $xmlContent = simplexml_load_file($xmlFile);
            if ($xmlContent === false) {
                return back()->withErrors('Failed to load XML.');
            }

            $namespaces = $xmlContent->getNamespaces(true);
            $xmlContent->registerXPathNamespace('w', $namespaces['w']);

        
            $tables = $xmlContent->xpath('//w:tbl');
            $TableArrayFile = [];
        

            foreach ($tables as $table) {
                $rows = $table->xpath('.//w:tr');
                foreach ($rows as $row) {
                    $rowData = [];
                    $cells = $row->xpath('.//w:tc');
                    
                    if (count($cells) >= 4) {
                        foreach ($cells as $cell) {
                            $texts = $cell->xpath('.//w:t');
                            $textContent = implode(' ', array_map('strval', $texts));
                            $textContent = trim($textContent);
                            if (strpos($textContent, 'to') !== false) {
                                $dateParts = explode('to', $textContent);
                                $dateParts = array_map('trim', $dateParts);
                                if (count($dateParts) == 2) {
                                    $startDate = $dateParts[0];
                                    $endDate = $dateParts[1];
                                    $startDate = preg_replace('/[^0-9-]/', '', $startDate); 
                                    $endDate = preg_replace('/[^0-9-]/', '', $endDate); 
                                    $rowData[] = $startDate . ' to ' . $endDate;
                                } else {
                                    $rowData[] = $textContent; 
                                }
                            } else {
                                $rowData[] = $textContent;
                            }
                        }
            
                        
                        if (isset($rowData[2]) && strpos($rowData[2], 'to') !== false) {
                            $dateRange = $rowData[2];
                            $dateRangeParts = explode('to', $dateRange);
                            $startDate = trim($dateRangeParts[0]);
                            $endDate = trim($dateRangeParts[1]);
                            $startDate = preg_replace('/[^0-9-]/', '', $startDate); 
                            $endDate = preg_replace('/[^0-9-]/', '', $endDate);
                            $rowData[2] = $startDate . ' to ' . $endDate;
                        }
            
                        $TableArrayFile[] = $rowData;
                    }else{

                        foreach ($cells as $cell) {
                            
                            $texts = $cell->xpath('.//w:t');
                            $textContent = implode(' ', array_map('strval', $texts));
                            $textContent = trim($textContent);
                
                            
                            $rowData[] = $textContent;
                        }
                        if (!empty($rowData)) {
                            $TableArray[] = $rowData;
                        }
                    }
                }
            }

            $columnTable = $this->arrangeDataCol($TableArray);
            $rowTable = $this->arrangeDataRow($TableArrayFile);
            $mergedArray = array_merge($columnTable, $rowTable);
       
        $this->insertTargetReport($mergedArray,$validated['student_id']);

        } catch (Exception $e) {
            
                $errorMessage = $e->getMessage();
                $datetimeError = '';
                
               
                if (strpos($errorMessage, 'Invalid datetime format') !== false) {
                    
                    preg_match("/Invalid datetime format:.*Incorrect date value: '[^']*'/", $errorMessage, $matches);
                    if (!empty($matches)) {
                        $datetimeError = $matches[0];
                    }
                }
                
                return response()->json([ 'errors' => ['upload_error' => [$datetimeError ? $datetimeError : 'An unexpected error occurred.']]], 400);
        }
    }

    public function insertTargetReport($mergedArray, $studentId)
    {
        $insertData = [];
        foreach ($mergedArray as $entry) {
            $insertData[] = [
                'student_id' => $studentId,
                'start_date' => $entry['start_date'],
                'end_date' => $entry['end_date'],
                'target' => $entry['target'],
                'created_at' => now(), 
                'updated_at' => now(),
            ];
        }

        TargetReport::insert($insertData);
    }


    public function generateReport(Request $request)
    {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'from_date' => 'required|date',
                'to_date' => 'required|date',
            ]);

            $getTemplateReport = $this->templateReplace($validated);
            if($getTemplateReport == "Target Improvement" || $getTemplateReport == "Session")
            {
                return response()->json([ 'errors' => ['message' => ['No '. $getTemplateReport.' found for the selected student within the specified date range.']]], 400);
            }

            Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true]);
            for ($i=1;$i<=round(15/$request->split_session);$i++) {

                $pdf = Pdf::loadHTML($getTemplateReport);
                $filename = 'report_' . ($i + 1).'-'.time(). '.pdf';
                    $path = storage_path('app/public/' . $filename);
                    $pdf->save($path);

                    $pdfFiles[] = $path;
            }

            $zipFile = 'documents'.time().'.zip';
            $zip = new \ZipArchive();
            $zipPath = storage_path('app/public/'.$zipFile);
            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                foreach ($pdfFiles as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();
            }

            foreach ($pdfFiles as $file) {
                unlink($file);
            }
            
            return response()->json([
                'zip_file_path' => asset('storage/'.$zipFile)
            ]);
    }
}

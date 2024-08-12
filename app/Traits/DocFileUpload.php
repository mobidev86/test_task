<?php

namespace App\Traits;

use App\Models\ReportTemplate;
use App\Models\ScheduleSession;
use App\Models\TargetReport;

trait DocFileUpload {

    public function arrangeDataRow($TableArrayFile)
    {
        array_shift($TableArrayFile);
        $formattedArray = [];
        foreach ($TableArrayFile as $item) {
            if(count($item) == 5)
            {
                $formattedArray[] = [
                   
                    'start_date' => $item[2],
                    'end_date' => $item[3],
                    'target' => $item[4]
                ];
            }else{
                
                $dateRange = explode(' to ', $item[2]);
                $target = trim(explode(' ', $item[3])[0]);
                
                $formattedArray[] = [
                    
                    'start_date' => $dateRange[0],
                    'end_date' => $dateRange[1],
                    'target' => $target
                ];
            }
        }

        return $formattedArray;
    }

    public function arrangeDataCol($TableArray)
    {
        $processedData = [];
        $currentEntry = [];
       
        foreach ($TableArray as $key => $data) {
           
            if (count($data) > 2) continue;
            $rowLabel = trim($data[0]);
           
            switch ($rowLabel) {
                case 'Session start date':
                case 'Start Date':
                case 'Session  start  date':
                    $rowValue = $TableArray[$key+1];
                   
                    $currentEntry['start_date'] = $rowValue[0] ? $this->formatDate($rowValue[0]) : '';
                    break;
                case 'Session end date':
                case 'End Date':
                    $rowValue = $TableArray[$key+1];
                    $currentEntry['end_date'] = $rowValue[0] ? $this->formatDate($rowValue[0]) : ''; 
                    break;
                case 'Improvement target':
                case 'target':
                    $rowValue = $TableArray[$key+1];
                    $currentEntry['target'] = $rowValue[0]??'';
                    break;
                default:
                    break;
            }

            if (isset($currentEntry['start_date']) && isset($currentEntry['end_date']) && isset($currentEntry['target'])) {
                $processedData[] = $currentEntry;
                $currentEntry = [];
            }
            
        }

        return $processedData;
    }

    public function formatDate($date) {
        $date = str_replace(' ', '', $date);
        $date = preg_replace('/(\d{4})-(\d{1,2})-(\d{1,2})/', '$1-$2-$3', $date);
        return $date;
    }


    public function templateReplace($validated)
    {
        $reportTemplate = ReportTemplate::first();
        $targetReport = TargetReport::with("student")->where("student_id", $validated["student_id"])
        ->whereBetween('start_date', [$validated["from_date"], $validated["to_date"]])
        ->whereBetween('end_date', [$validated["from_date"], $validated["to_date"]])
        ->first();

        $scheduleSession = ScheduleSession::where('student_id', $validated['student_id'])
                    ->whereBetween('session_date', [$validated['from_date'], $validated['to_date']])
                    ->whereNotNull('rating')
                    ->first();
            
       
        $name = ($targetReport->student->first_name ?? '').' '.($targetReport->student->middle_name ?? '').' '.($targetReport->student->last_name ?? '');
        $values = [
            '@student_full_name' => $name,
            '@session_date' => $scheduleSession->session_date??'@session_date',
            '@session_minutes' => '@session_minutes',
            '@session_start_time' => $scheduleSession->start_time??'@session_start_time',
            '@session_end_time' => $scheduleSession->end_time??'@session_end_time',
            '@target_start_date' => $targetReport->start_date??'@target_start_date',
            '@target_end_date' => $targetReport->end_date??'@target_end_date',
            '@target' => $targetReport->target??'@target',
            '@session_rating' => $scheduleSession->rating??'@session_rating'
        ];
        $template = $reportTemplate->template;
        foreach ($values as $placeholder => $value) {
            $template = str_replace($placeholder, $value, $template);
        }
       return $template;
    }
}

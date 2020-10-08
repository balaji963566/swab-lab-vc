<?php

namespace App\Exports;

use App\Inward;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromArray,WithHeadings
{
	protected $report_data;

    public function __construct(array $report_data)
    {
        $this->report_data = $report_data;
    }

    public function headings(): array
    {
        return [
            'Sample ID',
            'Patient name',
            'Age (Yrs)',
            'Gender',
            'Date of sample testing',
            'status'
        ];
    }

    public function array(): array
    {
        return $this->report_data;
    }
}
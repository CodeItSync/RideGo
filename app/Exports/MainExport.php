<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MainExport implements FromArray
{
    public $from;
    public $to;
    
    public function __construct($from, $to) {
        $this->from = $from;
        $this->to = $to;
    }
    
    public function array(): array
    {
        return [
            (new DriversExportSheet($this->from, $this->to))->array(),
            [' '],
            (new RidersExportSheet($this->from, $this->to))->array(),
            [' '],
            (new DashboardExportSheet($this->from, $this->to))->array(),
        ];
    }
}

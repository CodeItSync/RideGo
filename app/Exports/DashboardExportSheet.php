<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use App\Models\RideRequest;
use App\Models\Payment;
use App\Models\Complaint;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DashboardExportSheet implements FromArray, WithStyles
{
    public $from;
    public $to;

    public function __construct($from = null, $to = null) {
        $this->from = $from ? Carbon::parse($from) : null;
        $this->to = $to ? Carbon::parse($to) : null;
    }

    public function array(): array
    {
        return [
            $this->title(),
            $this->headings(),
            $this->getStatistics()
        ];
    }

    protected function getStatistics(): array
    {
        return [
            'pending_driver' => User::where('user_type', 'driver')
                ->where('is_verified_driver', 0)
                ->whereBetween('created_at', [$this->from, $this->to])
                ->count(),

            'total_driver' => User::getUser('driver')
                ->whereBetween('created_at', [$this->from, $this->to])->count(),
            'total_rider' => User::getUser('rider')
                ->whereBetween('created_at', [$this->from, $this->to])->count(),
            'total_ride' => RideRequest::query()
                ->whereBetween('created_at', [$this->from, $this->to])->count(),

            'today_earning' => Payment::where('payment_status', 'paid')
                ->when(!$this->from && !$this->to, function($query) {
                    $query->whereDate('datetime', Carbon::today());
                })
                ->when($this->from && $this->to, function($query) {
                    $query->whereBetween('datetime', [$this->from, $this->to]);
                })
                ->sum('total_amount'),

            'monthly_earning' => Payment::where('payment_status', 'paid')
                ->when(!$this->from && !$this->to, function($query) {
                    $query->whereMonth('datetime', Carbon::now()->month);
                })
                ->when($this->from && $this->to, function($query) {
                    $query->whereBetween('datetime', [$this->from, $this->to]);
                })
                ->sum('total_amount'),

            'total_earning' => Payment::where('payment_status', 'paid')
                ->when($this->from && $this->to, function($query) {
                    $query->whereBetween('datetime', [$this->from, $this->to]);
                })
                ->sum('total_amount'),

            'complaint' => Complaint::where('status', 'pending')
                ->whereBetween('created_at', [$this->from, $this->to])->count()
        ];
    }

    public function headings(): array
    {
        return ['Pending drivers', 'Totla drivers', 'Total riders', 'Total ride', 'Today earning', 'Monthly earning', 'Total earning', 'Complaint'];
    }

    public function title(): array
    {
        return ['Dashboard information'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // title
            2 => ['font' => ['bold' => true]],               // heading
        ];
    }
}

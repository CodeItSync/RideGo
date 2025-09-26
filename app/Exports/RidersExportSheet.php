<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class RidersExportSheet implements FromArray, WithStyles
{
    public $from;
    public $to;
    
    public function __construct($from = null, $to = null) {
        $this->from = $from ? Carbon::parse($from) : null;
        $this->to = $to ? Carbon::parse($to) : null;
    }
    
    public function array(): array
    {
        $riders = User::where('user_type', 'rider')
            ->where('status', 'active')
            ->with('roles', 'userBankAccount', 'userWallet')
            ->whereBetween('created_at', [$this->from, $this->to])
            ->get();
    
        $ridersSheet = [
            $this->title(),
            $this->headings(),
        ];
    
        foreach ($riders as $rider) {
            $totalWallet = optional($rider->userWallet)->total_amount ?? 0;
            $totalWithdraw = optional($rider->userWallet)->total_withdraw ?? 0;
    
            // If you want to filter transactions by $from and $to, you can do it here
    
            $ridersSheet[] = [
                $rider->display_name, 
                $rider->contact_number,
                getPriceFormat($totalWallet),
                getPriceFormat($rider->cash_collected_from_wallet ?? 0),
                getPriceFormat($totalWithdraw),
            ];
        }
    
        return $ridersSheet;
    }

    public function headings(): array
    {
        return ['Name', 'Phone number', 'Wallet balance', 'Commission earning', 'Total withdraw'];
    }

    public function title(): array
    {
        return ['Riders'];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // title
            2 => ['font' => ['bold' => true]],               // heading
        ];
    }
}
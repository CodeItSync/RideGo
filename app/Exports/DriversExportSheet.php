<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DriversExportSheet implements FromArray, WithStyles
{
    public $from;
    public $to;
    
    public function __construct($from = null, $to = null) {
        $this->from = $from ? Carbon::parse($from) : null;
        $this->to = $to ? Carbon::parse($to) : null;
    }
    
    public function array(): array
    {
        $drivers = User::where([
            'status' => 'active',
            'user_type' => 'driver'
        ])
        ->with('roles','userDetail', 'userBankAccount', 'driverRating', 'userWallet', 'service')
        ->whereBetween('created_at', [$this->from, $this->to])
        ->get()
        ->map(function($driver) {
            // Calculate rating
            $driver->rating = $driver->driverRating->count() > 0
                ? (float) number_format($driver->driverRating->avg('rating'), 2)
                : 0;
    
            // Base query for payments
            $paymentQuery = Payment::whereHas('riderequest', function($q) use ($driver) {
                $q->where('driver_id', $driver->id);
            })->where('payment_status', 'paid')
              ->when($this->from && $this->to, function($q) {
                  $q->whereBetween('datetime', [$this->from, $this->to]);
              });
    
            // Earnings breakdown
            $driver->cash_earning = (float) $paymentQuery->where('payment_type', 'cash')
                ->sum(DB::raw('admin_commission + driver_commission')) ?? 0;
    
            $driver->wallet_earning = (float) $paymentQuery->where('payment_type', 'wallet')
                ->sum('driver_commission') ?? 0;
    
            $driver->admin_commission = (float) $paymentQuery->sum('admin_commission') ?? 0;
    
            $driver->total_earning = $driver->cash_earning + $driver->wallet_earning;
    
            $driver->driver_earning = (float) $paymentQuery->sum('driver_commission') ?? 0;
    
            return $driver;
        });
    
        $driversSheet = [
            $this->title(),
            $this->headings(),
        ];
    
        foreach ($drivers as $driver) {
            $driversSheet[] = [
                $driver->display_name,
                $driver->contact_number,
                optional($driver->service)->name,
                optional($driver->userDetail)->car_model,
                optional($driver->userDetail)->car_plate_number,
                optional($driver->userDetail)->car_production_year,
                optional($driver->userBankAccount)->bank_name,
                optional($driver->userBankAccount)->account_holder_name,
                optional($driver->userBankAccount)->account_iban,
                getPriceFormat($driver->total_earning),
                getPriceFormat($driver->cash_earning),
                getPriceFormat($driver->wallet_earning),
                getPriceFormat($driver->cash_collected_from_wallet ?? 0),
                getPriceFormat($driver->admin_commission),
                getPriceFormat($driver->driver_earning),
                getPriceFormat(optional($driver->userWallet)->total_amount),
                getPriceFormat(optional($driver->userWallet)->total_withdrawn),
            ];
        }
    
        return $driversSheet;
    }


    public function headings(): array
    {
        return ['Name', 'Phone number', 'Service name', 'Car model', 'Car plate number', 'Car production year', 'Bank name', 'Account holder name', 'Account IBAN', 'Total earning',
        'Cash earning', 'Wallet earning', 'Commission earning', 'Admin commission', 'Driver earning', 'Wallet balance', 'Total withdraw'];
    }

    public function title(): array
    {
        return ['Drivers'];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // title
            2 => ['font' => ['bold' => true]],               // heading
        ];
    }
}
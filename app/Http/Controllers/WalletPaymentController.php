<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Services\TapPayment;
use App\Notifications\FirebaseNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletPaymentController extends Controller
{
    public function success(Request $request)
    {
        if (!$request->has('tap_id')) abort(404);
        $tapPyament = new TapPayment();
        $response = $tapPyament->retrievePayment($request->tap_id);
        if (!$response['success']) abort(500);
        if ($response['response']->status !='CAPTURED') return redirect('payment-processed?status=' . $response['response']->status);
        $paidAmount = $response['response']->amount;
        $metaData = $response['response']->metadata;
        $data = json_decode($metaData->data);
        $type = $data->type;
        $tapPaymentData = $data->data;
        $id = $data->id;
        $wallet = Wallet::findOrFail($tapPaymentData->id);
        $user = User::find($wallet->user_id);
        $total_amount = $wallet->total_amount + $paidAmount;
        // $wallet->currency = $tapPaymentData->currency;
        try
        {
            DB::beginTransaction();
            $wallet->total_amount = $total_amount;
            $wallet->save();
            $user->save();
            $data->balance = $paidAmount;
            $data->datetime = date('Y-m-d H:i:s');
            $result = WalletHistory::updateOrCreate(['id' => $id], (array)$data);
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
        }
        return redirect('payment-processed?status=captured');
    }
}

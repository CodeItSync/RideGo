<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SkipCash;
use App\Services\TapPayment;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Notifications\FirebaseNotify;
use App\Http\Resources\WalletHistoryResource;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function saveWallet(Request $request)
    {
        $data = $request->all();
        $user_id = request()->user_id ?? auth()->user()->id;
        $data['user_id'] = $user_id;
        $wallet =  Wallet::firstOrCreate(
            [ 'user_id' => $user_id ]
        );

        if( $data['type'] == 'credit' ) {
            $total_amount = $wallet->total_amount + $data['amount'];
        }

        if( $data['type'] == 'debit' ) {
            $total_amount = $wallet->total_amount - $data['amount'];
        }
        $wallet->currency = $data['currency'];
        $wallet->total_amount = $total_amount;
        $message = __('message.save_form',[ 'form' => __('message.wallet') ] );
        // $skipCash = new SkipCash();
        $tapPayment = new TapPayment();
        $user = User::findOrfail($user_id);
        $phone = substr($user->contact_number, 4);
        // $skipCashData = [
        //     'Amount' => (string)$total_amount,
        //     'FirstName' => $user->first_name,
        //     'LastName' => $user->last_name,
        //     'Phone' => $phone,
        //     'Email' => 'mayyash933@gmail.com',
        //     'TransactionId' => (string)rand(100000, 9999999),
        //     'Custom1' => json_encode([
        //         'type' => 'wallet',
        //         'data' => $wallet,
        //         'id' => $request->id,
        //     ]),
        // ];
        $countryCode = '974';
        $phoneNumber = '12345678';
        if (preg_match('/\+(\d+)-(\d+)/', $phone, $matches)) {
            $countryCode = $matches[1];
            $phoneNumber = $matches[2];
        }
        $tapPaymentData = [
            'amount' => $data['amount'],
            'currency' => 'QAR',
            'threeDSecure' => true,
            'save_card' => false,
            'description' => 'Add to wallet',
            'source' => [
                'id' => $request->source_type ?? 'src_card',
            ],
            'metadata' => [
                'transaction_id' =>    (string)rand(100000, 9999999),
                'data' => json_encode([
                    'type' => 'wallet',
                    'data' => $wallet,
                    'id' => $request->id,
                ]),
            ],
            'redirect' => [
                'url' => 'https://ride-go.net/payment-success',    
            ],
            'customer' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => 'mayyash933@gmail.com',
                'phone' => [
                    'country_code'    => $countryCode,
                    'number' => $phoneNumber,
                ],
            ],
        ];
        $pay = $tapPayment->makePayment($tapPaymentData);
        if (!$pay['success']) {
            return json_custom_response(['message' => $pay['response']->errors[0]->description], 400);
        }
        $response = $pay['response'];
        $transaction = $response->transaction;
        return json_message_response($transaction->url);
    }

    public function getList(Request $request)
    {
        $wallet = WalletHistory::myWalletHistory();

        $wallet->when(request('user_id'), function ($q) {
            return $q->where('user_id', request('user_id'));
        });

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){

            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }

            if($request->per_page == -1 ){
                $per_page = $wallet->count();
            }
        }

        $wallet = $wallet->orderBy('id','desc')->paginate($per_page);
        $items = WalletHistoryResource::collection($wallet);

        $wallet_data = Wallet::where('user_id', auth()->user()->id)->first();
        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
            'wallet_balance' => $wallet_data
        ];

        return json_custom_response($response);
    }

    public function getWallatDetail(Request $request)
    {
        $wallet_data = Wallet::where('user_id', auth()->user()->id)->first();

        if( $wallet_data == null ) {
            $message = __('message.not_found_entry',['name' => __('message.wallet')]);
            return json_message_response($message,400);
        }
        $min_amount_to_get_ride = SettingData('wallet', 'min_amount_to_get_ride') ?? 0;
        $response = [
            'wallet_data' => $wallet_data ?? null,
            'min_amount_to_get_ride' => (int) $min_amount_to_get_ride,
            'total_amount'  => $wallet_data->total_amount,
        ];
        return json_custom_response($response);
    }
}

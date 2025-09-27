<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\RewardDataTable;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Models\Reward;
use App\Notifications\FirebaseNotify;

class RewardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('user_type')) {
            $userType = $request->user_type == 'driver' ? 'driver' : 'rider';
            $pageTitle = $userType == 'driver' ? __('message.drivers_reward') : __('message.riders_reward');
        } else {
            $userType = null;
            $pageTitle = __('message.all_rewards');
        }
        $dataTable = app(RewardDataTable::class);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = $userType == 'rider' || $userType == 'driver' ? view('reward.reward_all') : '';
        return $dataTable->with('userType', $userType)->render('global.datatable', compact('assets','pageTitle','button','auth_user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $amount = $request->amount;
        if (!$amount or $amount <= 0) return back()->withError('please add an amount');
        $textArabic = "مبروك٬ تمت اضافة $amount ريال الى محفظتك";
        $textEnglish = "Congratulations, $amount QAR added tp your wallet";
        $users = [];
        $currency_code = SettingData('CURRENCY', 'CURRENCY_CODE') ?? 'USD';
        $currency_data = currencyArray($currency_code);
        $currency = strtolower($currency_data['code']);
        if ($request->has('id') and $request->id) {
            $user = User::where('id', $request->id)->first();
            $this->updateWallet($user->id, $amount, $currency);
            if ($user->lang == 'ar') {
                $text = $textArabic;
            } else {
                $text = $textEnglish;
            }
            $user->notify(new FirebaseNotify(['title' => 'RideGo', 'body' => "$text", 'data' => [
                'clickable' => '1',
            ]]));
        } else {
            $users = User::where(['status' => 'active', 'user_type' => $request->user_type ?? 'rider']);
            if ($request->user_ids) {
                $users = $users->whereIn('id', explode(',', $request->user_ids));
            }
            $users->chunk(100, function ($users) use($amount, $currency, $textEnglish, $textArabic) {
                foreach ($users as $user) {
                    if ($user->lang == 'ar') {
                        $text = $textArabic;
                    } else {
                        $text = $textEnglish;
                    }
                    $this->updateWallet($user->id, $amount, $currency);
                    $user->notify(new FirebaseNotify(['title' => 'RideGo', 'body' => "$text", 'data' => [
                        'clickable' => '1',
                    ]]));
                }
            });
        }
        return redirect()->back()->withSuccess(__('message.save_form', ['form' => '']));
    }

    public function updateWallet($userId, $amount, $currency) {
        $wallet =  Wallet::firstOrCreate(
            [ 'user_id' => $userId ]
        );
        $wallet->total_amount = ($wallet->total_amount ?? 0) + $amount;
        $wallet->save();
        $wallet_history = [
            'user_id' => $userId,
            'type' => 'credit',
            'currency' => $currency,
            'transaction_type' => 'reward',
            'amount' => $amount,
            'balance' => $wallet->total_amount,
            'datetime'  => date('Y-m-d H:i:s'),
        ];

        WalletHistory::create($wallet_history);
        Reward::create([
            'user_id' => $userId,
            'amount' => $amount,
        ]);
    }

    public function users_search(Request $request)
    {
        $search = $request->input('q');
        $users = User::query()
        ->select('id', 'display_name as name')
            ->where([
                'status' => 'active',
                'user_type' => $request->user_type ?? 'rider',
            ])
            ->when($search, fn($q) => $q->where('display_name', 'like', "%{$search}%"))
            ->paginate(20);

        return response()->json([
            'items' => $users->items(),
            'pagination' => [
                'more' => $users->hasMorePages(),
            ]
        ]);
    }
}

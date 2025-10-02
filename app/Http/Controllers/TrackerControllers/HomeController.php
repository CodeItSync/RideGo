<?php

namespace App\Http\Controllers\TrackerControllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    /*
     * Dashboard Pages Routs
     */
    public function index(Request $request)
    {
        $auth_user = auth('tracker')->user();

        $pageTitle = __('message.driver_location');
        $assets = ['map'];
        return view('tracker-dashboard.map.index', compact('pageTitle','assets'));
    }

    public function changeLanguage($locale)
    {
        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }

    function authLogin()
    {
        if (request()->isMethod('post')) {
            $credentials = request()->only('email', 'password');
            if (auth('tracker')->attempt($credentials)) {
                return redirect()->intended(route('tracker.index'));
            }
            return redirect()->back()->withErrors(['error' => __('Invalid Credentials')])->withInput();
        }
        return view('auth.tracker_login');
    }

    public function driverDetail(Request $request)
    {
        $driver = User::with(['driverRideRequestDetail' => function ($query) {
            $query->whereIn('status', ['accepted', 'arriving', 'arrived', 'in_progress'])
                ->latest()
                ->limit(1);
        }])->where('id', request('id'))->first();
        return new DriverResource($driver);
    }

    public function logout()
    {
        auth('tracker')->logout();
        return redirect()->route('tracker.auth.login');
    }
}

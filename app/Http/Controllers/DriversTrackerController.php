<?php

namespace App\Http\Controllers;

use App\DataTables\DriversTrackerDataTable;
use App\Http\Requests\DriversTrackerRequest;
use App\Models\DriverTracker;

class DriversTrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DriversTrackerDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('Drivers tracker')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = '<a href="' . route('drivers_tracker.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('Drivers tracker')]) . '</a>';
        return $dataTable->render('global.datatable', compact('assets', 'pageTitle', 'button', 'auth_user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriversTrackerRequest $request)
    {
        $request->merge(['password' => bcrypt($request->password)]);
        $driverTracker = DriverTracker::create($request->all());
        return redirect()->route('drivers_tracker.index')->withSuccess(__('message.save_form', ['form' => __('Drivers tracker')]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('Drivers tracker')]);
        $assets = ['phone'];
        return view('drivers_tracker.form', compact('pageTitle', 'assets'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = __('message.update_form_title', ['form' => __('Drivers Tracker')]);
        $data = DriverTracker::findOrFail($id);
        $data->password = null;
        return view('drivers_tracker.form', compact('data', 'pageTitle', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(DriversTrackerRequest $request, $id)
    {
        $driverTracker = DriverTracker::findOrFail($id);

        $request->merge(['password' => bcrypt($request->password)]);
        $driverTracker->fill($request->all())->update();

        if (auth()->check()) {
            return redirect()->route('drivers_tracker.index')->withSuccess(__('message.update_form', ['form' => __('Drivers tracker')]));
        }
        return redirect()->back()->withSuccess(__('message.update_form', ['form' => __('Drivers Tracker')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (env('APP_DEMO')) {
            $message = __('message.demo_permission_denied');
            if (request()->ajax()) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            return redirect()->route('drivers_tracker.index')->withErrors($message);
        }
        $driverTracker = DriverTracker::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('Drivers tracker')]);

        if ($driverTracker != '') {
            $driverTracker->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('Drivers tracker')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}

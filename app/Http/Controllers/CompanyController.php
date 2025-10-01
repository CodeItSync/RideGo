<?php

namespace App\Http\Controllers;

use App\DataTables\CompanyDataTable;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CompanyDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('company')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = '<a href="' . route('companies.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('company')]) . '</a>';
        return $dataTable->render('global.datatable', compact('assets', 'pageTitle', 'button', 'auth_user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        $company = Company::create($request->all());
        return redirect()->route('companies.index')->withSuccess(__('message.save_form', ['form' => __('company')]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('company')]);
        $assets = ['phone'];
        return view('company.form', compact('pageTitle', 'assets'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = __('message.update_form_title', ['form' => __('company')]);
        $data = Company::findOrFail($id);
        return view('company.form', compact('data', 'pageTitle', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, $id)
    {
        $company = Company::findOrFail($id);

        $company->fill($request->all())->update();

        if (auth()->check()) {
            return redirect()->route('companies.index')->withSuccess(__('message.update_form', ['form' => __('company')]));
        }
        return redirect()->back()->withSuccess(__('message.update_form', ['form' => __('company')]));
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
            return redirect()->route('company.index')->withErrors($message);
        }
        $company = Company::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('company')]);

        if ($company != '') {
            $company->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('company')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function userList(Request $request)
    {
        $items = CompanyResource::collection(
            Company::all()
        );

        return json_custom_response($items);
    }
}

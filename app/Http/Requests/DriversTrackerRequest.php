<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class DriversTrackerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route()->drivers_tracker ?? null;
        return [
            'name' => 'required|max:255|string',
            'email' => 'required|max:255|email|unique:driver_trackers,email,' . $id,
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:6'],
        ];
    }

    public function messages()
    {
        return [ ];
    }
}

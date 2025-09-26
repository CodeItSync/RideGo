<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class DriverRequest extends FormRequest
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
        $method = strtolower($this->method());
        $user_id = $this->route()->driver;

        $rules = [];
        switch ($method) {
            case 'post':
                $rules = [
                    // 'username' => 'required|unique:users,username',
                    'password' => 'required|min:8',
                    'contact_number' => 'required|max:20|unique:users,contact_number',
                    'dial_code' => 'required|max:4'
                    // 'firebaseIdToken' => 'required|string',
                ];
                break;
            case 'patch':
                $rules = [
                    // 'username'  => 'required|unique:users,username,'.$user_id,
                    'contact_number' => 'required|max:20|unique:users,contact_number,'.$user_id,
                ];
                break;
        }
        $dialCode = $this->request->get('dial_code');
        $contactNumber = '+' . $dialCode . '-' . str_replace(' ', '', $this->request->get('contact_number'));
        $this->merge(['contact_number' => $contactNumber]);

        return $rules;
    }

    public function messages()
    {
        return [
            'userProfile.dob.*'  =>'DOB is required.',
        ];
    }

     /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        $data = [
            'status' => true,
            'message' => $validator->errors()->first(),
            'all_message' =>  $validator->errors()
        ];

        if ( request()->is('api*')){
           throw new HttpResponseException( response()->json($data,422) );
        }

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json($data,422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}

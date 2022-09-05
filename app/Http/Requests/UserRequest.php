<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\PhoneNumber;

class UserRequest extends FormRequest
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
        $user_id = $this->id ?? null;

        return [
            'name'  => 'required|string|max:40',
            'email' => 'required|email:rfc,dns|unique:users,email,'.$user_id. ',id',
            'phone' => ['required', new PhoneNumber],
            'address'  => 'nullable|string|max:200',
            'role' => 'required|integer',
            'seller_id' => 'nullable',
        ];

        if (request()->isMethod('put') || request()->isMethod('patch')) {
            $rules['password'] = "nullable|min:6";
        }
        if (request()->isMethod('post')) {
            $rules['password'] = "required|min:6";
        }
    }
}

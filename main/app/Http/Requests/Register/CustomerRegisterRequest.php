<?php

namespace App\Http\Requests\Register;

use App\Contracts\Requests\UserRegisterRequestContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class CustomerRegisterRequest extends FormRequest implements UserRegisterRequestContract
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
        return [
          'name' => ['required', 'min:2', 'max:255'],
          'email' => ['required', 'max:128', 'unique:users'],
          'password' => ['required', 'min:6', 'max:255'],
          'role_id' => ['required'],
        ];
    }

    /** method "passedValidation" does not merge values for some reason,
     * so this one is used
     */
    public function validated()
    {
        $request = $this->validator->validated();

        $request['password'] = Hash::make($this->password);

        return $request;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.unique' => 'Такая почта уже существует',
        ];
    }
}

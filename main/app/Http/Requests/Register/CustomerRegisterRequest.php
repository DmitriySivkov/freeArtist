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

    public function prepareForValidation()
	{
		$this->merge([
			'phone' => preg_replace('/\D/', '', $this->phone)
		]);
	}

	/**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'phone' => ['required', 'max:32', 'unique:users'],
          'password' => ['required', 'min:6', 'max:255'],
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
            'phone.unique' => 'Пользователь с таким номером уже существует',
        ];
    }
}

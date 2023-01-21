<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProducerRegisterRequest extends FormRequest
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
			'display_name' => ['required', 'unique:teams,display_name'],
			'city_id' => ['nullable', 'exists:cities,id']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'display_name.unique' => 'Такое название уже существует',
        ];
    }
}

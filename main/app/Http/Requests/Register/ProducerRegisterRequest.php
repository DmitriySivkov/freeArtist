<?php

namespace App\Http\Requests\Register;

use App\Contracts\Requests\RegisterRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class ProducerRegisterRequest extends FormRequest implements RegisterRequestContract
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
			'producer' => ['required', 'unique:producers,title']
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
            'producer.unique' => 'Такой изготовитель уже существует',
        ];
    }
}

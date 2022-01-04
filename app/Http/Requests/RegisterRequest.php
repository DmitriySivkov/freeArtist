<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      Log::info(request()->all());
        return [
          'name' => ['required', 'min:6', 'max:255'],
          'email' => ['required', 'max:128', 'unique'],
          'password' => ['required', 'min:6', 'max:255'],
          'roles' => ['required', 'json'],
        ];
    }
}

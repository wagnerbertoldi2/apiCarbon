<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PassResetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        return [
            'email' => ['required', 'string', 'email', 'max:50'],
            'code' => 'required',
            'password' => ['required', 'string', 'min:8', 'max:150'],
        ];
    }

    public function messages(){
        return [
            'email.required' => 'O campo E-mail é obrigatório.',
            'email.email' => 'O E-mail informado não é válido.',
            'email.max' => 'O E-mail não pode ter mais de 50 caracteres.',
            'password.required' => 'O campo Senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.max' => 'A senha deve no máximo 150 caracteres.',
            'code.required' => 'O campo Código é obrigatório.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) : void{
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}

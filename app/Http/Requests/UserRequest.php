<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest{
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
        $rules= [
            'firstName' => ['required','string', 'max:50'],
            'lastName' => ['required', 'string', 'max:50'],
            'cpf' => ['required', 'string', 'max:15', 'unique:users'],
            'rg' => ['required', 'string', 'max:30', 'unique:users'],
            'cnpj' => ['nullable', 'string', 'max:30', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:150'],
        ];

        return $rules;
    }

    public function messages(){
        return [
            'firstName.required' => 'O campo Nome é obrigatório.',
            'lastName.required' => 'O campo Sobrenome é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já foi cadastrado.',
            'rg.required' => 'O campo RG é obrigatório.',
            'rg.unique' => 'Este RG já foi cadastrado.',
            'email.required' => 'O campo E-mail é obrigatório.',
            'email.email' => 'O E-mail informado não é válido.',
            'email.unique' => 'Este E-mail já foi cadastrado.',
            'password.required' => 'O campo Senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) : void{
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

}

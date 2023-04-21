<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PropertyRequest extends FormRequest{
    public function authorize(){
        return true;
    }

    public function rules(){
        return [
            'Name' => 'required|string',
            'Registration' => 'required|string',
            'CEP' => 'required|string',
            'City' => 'required|string',
            'Number' => 'required|string',
            'Complement' => 'nullable|string',
            'NumberOfPeoples' => 'required|integer',
            'Address' => 'required|string',
            'UF' => 'required|string',
            'UserId' => 'required|integer',
            'CategoryId' => 'required|integer'
        ];
    }

    public function messages(){
        return [
            'Name.required' => 'O campo Nome é obrigatório',
            'Registration.required' => 'O campo Registro é obrigatório',
            'CEP.required' => 'O campo CEP é obrigatório',
            'City.required' => 'O campo Cidade é obrigatório',
            'Number.required' => 'O campo Número é obrigatório',
            'NumberOfPeoples.required' => 'O campo Número de pessoas é obrigatório',
            'Address.required' => 'O campo Endereço é obrigatório',
            'UF.required' => 'O campo UF é obrigatório',
            'UserId.required' => 'O campo Usuário é obrigatório',
            'CategoryId.required' => 'O campo Categoria é obrigatório'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) : void{
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}

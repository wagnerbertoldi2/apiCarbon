<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PropertyRequest extends FormRequest{
    public function authorize(){
        return true;
    }

    public function rules(){
        $propertyId= $this->route('property');

        return [
            'Name' => 'required|string',
            'Registration' => 'required|string',
            'CEP' => 'required|string',
            'City' => 'required|string',
            'Number' => [
                'required',
                'string',
                Rule::unique('property')
                    ->where('CEP', $this->input('CEP'))
                    ->where('Complement', $this->input('Complement'))
                    ->ignore($propertyId),
                ],
            'Complement' => 'nullable|string',
            'NumberOfPeoples' => 'required|integer',
            'Address' => 'required|string',
            'UF' => 'required|string',
            'UserId' => 'integer',
            'CategoryId' => 'required|integer'
        ];
    }

    public function messages(){
        return [
            'required' => 'O campo :attribute é obrigatório',
            'unique' => 'Este imóvel já está registrado.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) : void{
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}

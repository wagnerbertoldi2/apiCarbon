<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequest extends FormRequest
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
            'Name' => 'required|string|max:100|unique:Category,Name'
        ];
    }

    public function messages(){
        return [
            'Name.required' => 'O nome da categoria é obrigatório.',
            'Name.unique' => 'Este nome de categoria já existe.',
            'Name.max' => 'O nome da categoria deve ter no máximo :max caracteres.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) : void{
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmissionFactorRequest extends FormRequest
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
            'Name' => 'required',
            'UnitId' => 'required'
        ];
    }

    public function messages(){
        return [
            'Name.required' => 'Name is required',
            'UnitId.required' => 'UnitId is required'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) : void{
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}

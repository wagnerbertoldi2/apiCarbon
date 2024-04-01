<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmissionRequest extends FormRequest
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
            'Attachment' => 'file|max:10240|mimes:pdf,jpg,gif,png,jpeg',
            'Amount' => 'required',
            'InitialPeriod' => 'required',
            'FinalPeriod' => 'required',
            'EmissionSourceId' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'Attachment.required' => 'Attachment is required',
            'Amount.required' => 'Amount is required',
            'InitialPeriod.required' => 'Initial Period is required',
            'FinalPeriod.required' => 'Final Period is required',
            'EmissionSourceId.required' => 'Emission Source is required'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) : void{
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class EmissionSourceRequest extends FormRequest
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
            'Name' => 'required',
            'EmissionFactorId' => [
                'required',
                Rule::unique('emissionsource')->where(function ($query) {
                    return $query->where('PropertyId', $this->PropertyId);
                }),
            ],
            'PropertyId' => 'required',
            'PeriodId' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'Name.required' => 'Name is required',
            'EmissionFactorId.required' => 'Emission Source Id is required',
            'EmissionFactorId.unique' => 'Emission Source Id must be unique for each Property',
            'PropertyId.required' => 'Property Id is required',
            'PeriodId.required' => 'Period Id is required',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) : void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}

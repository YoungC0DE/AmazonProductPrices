<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as httpCodes;

class CreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'crawlerType' => 'required|string',
            'options.orderBy' => 'required|string',
            'options.totalPages' => 'required|integer',
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'error' => 'request failed',
                'messages' => $validator->errors(),
            ], httpCodes::HTTP_BAD_REQUEST)
        );
    }

    public function messages()
    {
        return [
            'crawlerType.required' => "is required.",
            'crawlerType.string' => "must be type string.",
            'options.orderBy.required' => "is required.",
            'options.orderBy.string' => "must be type string.",
            'options.totalPages.required' => "is required.",
            'options.totalPages.string' => "must be type string."
        ];
    }
}

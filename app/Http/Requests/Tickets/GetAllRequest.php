<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as httpCodes;

class GetAllRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => 'numeric|min:1',
            'platform' => 'string|in:AMAZON,MERCADO_LIVRE,OLX,EBAY',
            'status' => 'numeric|in:0,1,2,3',
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
                'error' => 'Error on request...',
                'messages' => $validator->errors(),
            ], httpCodes::HTTP_BAD_REQUEST)
        );
    }

    public function messages()
    {
        return [
            'page.numeric' => 'Page must be a number.',
            'page.min' => 'Page must be minimum 1',
            'platform.string' => 'Platform must be a string',
            'platform.in' => 'Platform must be one of: AMAZON, MERCADO_LIVRE, OLX, EBAY.',
            'status.numeric' => 'Page must be a number',
            'status.in' => 'Page must be in: 0 (Pending), 1 (Active), 2 (Running), 3 (Error)',
        ];
    }
}

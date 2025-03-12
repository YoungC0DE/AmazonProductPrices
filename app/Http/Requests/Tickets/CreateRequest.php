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
            'requestSettings.platform' => 'required|string|in:AMAZON,MERCADO_LIVRE,OLX,EBAY,SHOPEE',
            'requestSettings.searchQuery' => 'required|string|min:3',
            'filters.sortBy' => 'required|string|in:priceAscending,priceDescending,relevance',
            'filters.ratingAbove' => 'nullable|numeric|min:0|max:5',
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
                'error' => 'Error on sending request...',
                'messages' => $validator->errors(),
            ], httpCodes::HTTP_BAD_REQUEST)
        );
    }

    public function messages()
    {
        return [
            'requestSettings.platform.required' => "Platform is required.",
            'requestSettings.platform.string' => "Platform must be a string.",
            'requestSettings.platform.in' => "Platform must be one of: AMAZON, MERCADO_LIVRE, OLX, EBAY or SHOPEE.",
            'requestSettings.searchQuery.required' => "Search query is required.",
            'requestSettings.searchQuery.string' => "Search query must be a string.",
            'requestSettings.searchQuery.min' => "Search query must have at least 3 characters.",
            'filters.sortBy.required' => "sortBy is required.",
            'filters.sortBy.string' => "sortBy must be a string.",
            'filters.sortBy.in' => "sortBy must be one of: priceAscending, priceDescending, relevance.",
            'filters.ratingAbove.numeric' => "ratingAbove must be a number.",
            'filters.ratingAbove.min' => "ratingAbove must be at least 0.",
            'filters.ratingAbove.max' => "ratingAbove cannot be greater than 5.",
        ];
    }
}

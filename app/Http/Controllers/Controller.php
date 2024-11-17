<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as httpCodes;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseAccepted()
    {
        return response()->json([], httpCodes::HTTP_ACCEPTED);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseItem($data)
    {
        return response()->json(['item' => $data], httpCodes::HTTP_ACCEPTED);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson($data)
    {
        return response()->json([$data], httpCodes::HTTP_ACCEPTED);
    }

    /**
     * @param string $message
     * @param integer $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseError($message = 'Error', $code = httpCodes::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json(
            [
                'message' => $message
            ], 
            $code ?: httpCodes::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}

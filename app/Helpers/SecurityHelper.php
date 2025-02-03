<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class SecurityHelper {

    /**
     * @param Request $request
     * 
     * @return array|string|null
     */
    public static function getRealIp(Request $request)
    {
        if (!empty($request->server('HTTP_X_FORWARDED_FOR'))) {
            return $request->server('HTTP_X_FORWARDED_FOR');
        }

        return $request->server('REMOTE_ADDR');
    }
}
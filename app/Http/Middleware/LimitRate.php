<?php

namespace App\Http\Middleware;

use App\Helpers\SecurityHelper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\RateLimiter;

class LimitRate
{
    protected const CACHE_RATE_LIMIT_REQUEST = 'request_rate_%s_%s';

    protected $maxAttempts = 1;
    protected $decaySeconds = 5;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $action)
    {
        $userIp = SecurityHelper::getRealIp($request);

        $cacheAddress = sprintf(
            self::CACHE_RATE_LIMIT_REQUEST,
            $action,
            $userIp
        );

        $executed = RateLimiter::attempt(
            $cacheAddress,
            $this->maxAttempts,
            function() {
                return true;
            },
            $this->decaySeconds
        );

        if ($executed) {
            return $next($request);
        }

        $sleepTime = RateLimiter::availableIn($cacheAddress);

        $message = sprintf(
            'Try again in %s second(s)',
            Carbon::now()->addSeconds((int) $sleepTime)->diffInSeconds(),
        );

        return (new Controller())->responseTooManyRequests($message);
    }
}

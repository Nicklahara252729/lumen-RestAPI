<?php

namespace App\Http\Middleware;

/**
 * import component
 */

use Closure;
use Illuminate\Support\Str;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

class Signature
{
    private $checkerHelpers;

    public function __construct(
        CheckerHelpers $checkerHelpers
    ) {

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $signature = base64_decode($request->header('signature'));
        $checkUser = $this->checkerHelpers->userChecker(['uuid_user' => $signature]);
        $signature = Str::isUuid($signature);

        if ($signature != true || is_null($checkUser))
            return response(['status' => false, 'message' => 'invalid signature'], 401);

        return $next($request);
    }
}

<?php

/**
 * file collection
 */

namespace App\Http\Controllers\Auth\Logout;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import repositories
 */

use App\Repositories\Auth\Logout\LogoutRepositories;

class LogoutController extends Controller
{
    private $logoutRepositories;
    private $status;

    public function __construct(
        LogoutRepositories $logoutRepositories
    ) {
        /**
         * init middleware
         */
        $this->middleware('auth:api', ['except' => ['logout']]);

        /**
         * init repositories
         */
        $this->logoutRepositories = $logoutRepositories;

        /**
         * init static value
         */
        $this->status = 200;
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        /**
         * process
         */
        $response = $this->logoutRepositories->logout();
        $this->status = $response['status'] == false ? 500 : $this->status;

        /**
         * response
         */
        return response()->json($response, $this->status);
    }
}

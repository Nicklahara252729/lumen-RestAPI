<?php

/**
 * file collection
 */

namespace App\Http\Controllers\Auth\Login;

/**
 * import component
 */

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login\LoginRequest;

/**
 * import repositories
 */

use App\Repositories\Auth\Login\LoginRepositories;

class LoginController extends Controller
{
    private $loginRepositories;
    private $status;

    public function __construct(
        LoginRepositories $loginRepositories
    ) {
        /**
         * init middleware
         */
        $this->middleware('auth:api', ['except' => ['login']]);

        /**
         * init repositories
         */
        $this->loginRepositories = $loginRepositories;

        /**
         * init static value
         */
        $this->status = 200;
    }

    /**
     * login process
     */
    public function login(LoginRequest $loginRequest)
    {
        /**
         * requesting data
         */
        $loginRequest = $loginRequest->all();

        /**
         * process
         */
        $response = $this->loginRepositories->login($loginRequest);
        $this->status = $response['status'] == false ? 401 : $this->status;

        /**
         * response
         */
        return response()->json($response, $this->status);
    }
}

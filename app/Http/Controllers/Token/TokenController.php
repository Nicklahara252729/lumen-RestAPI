<?php

/**
 * file collection
 */

namespace App\Http\Controllers\Token;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import repositories
 */

use App\Repositories\Token\TokenRepositories;

class TokenController extends Controller
{
    private $tokenRepositories;
    private $status;

    public function __construct(
        TokenRepositories $tokenRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->tokenRepositories = $tokenRepositories;

        /**
         * init middleware
         */
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout']]);


        /**
         * init static value
         */
        $this->status = 200;
    }

    /**
     * validation token     
     */
    public function validation()
    {
        /**
         * process
         */
        $response = $this->tokenRepositories->validation();
        $this->status = $response['status'] == false ? 500 : $this->status;

        /**
         * response
         */
        return response()->json($response, $this->status);
    }

    /**
     * Refresh a token
     */
    public function refresh()
    {
        /** 
         * process
         */
        $response = $this->tokenRepositories->refresh();
        $this->status = $response['status'] == false ? 500 : $this->status;

        /**
         * response
         */
        return response()->json($response, $this->status);
    }
}

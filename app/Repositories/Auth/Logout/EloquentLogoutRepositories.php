<?php

namespace App\Repositories\Auth\Logout;

/**
 * import component
 */

use Illuminate\Http\Request;

/**
 * import traits
 */

use App\Traits\JwtResponse;
use App\Traits\Message;
use App\Traits\Response;

/**
 * import interface
 */

use App\Repositories\Auth\Logout\LogoutRepositories;
use App\Repositories\Log\LogRepositories;

class EloquentLogoutRepositories implements LogoutRepositories
{
    use Message, Response, JwtResponse;

    private $signature;
    private $logRepositories;

    public function __construct(
        Request $request,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * logout process
     */
    public function logout()
    {
        try {
            /**
             * log user
             */
            $message = $this->outputLogMessage('logout');
            $this->logRepositories->saveLog($message['action'], $message['message'], $this->signature, null);

            /**
             * logout process
             */
            auth()->logout();
            $response  = $this->success($this->outputMessage('logout'));
        } catch (\Throwable $e) {
            $response = $this->error($e->getMessage());
        }

        return $response;
    }
}

<?php

namespace App\Repositories\Token;

/**
 * import component 
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * import traits
 */

use App\Traits\JwtResponse;
use App\Traits\Response;
use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Token\TokenRepositories;
use App\Repositories\Log\LogRepositories;

class EloquentTokenRepositories implements TokenRepositories
{
    use JwtResponse, Response, Message;

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
     * validation token     
     */
    public function validation()
    {
        try {
            /**
             * log user
             */
            $message = $this->outputLogMessage('validation');
            $this->logRepositories->saveLog($message['action'], $message['message'], $this->signature, null);

            /**
             * respon validasi
             */
            $response = array_merge(
                $this->success('token is valid'),
                $this->tokenResponse()
            );
        } catch (\Throwable $e) {
            $response = $this->error($e->getMessage());
        }

        return $response;
    }

    /**
     * Refresh a token
     */
    public function refresh()
    {
        try {
            /**
             * log user
             */
            $message = $this->outputLogMessage('refresh');
            $this->logRepositories->saveLog($message['action'], $message['message'], $this->signature, null);

            /**
             * response refresh
             */
            $response = array_merge(
                $this->success('ok'),
                ['access_token' => Auth::refresh()]
            );
        } catch (\Throwable $e) {

            $response = $this->error($e->getMessage());
        }

        return $response;
    }
}

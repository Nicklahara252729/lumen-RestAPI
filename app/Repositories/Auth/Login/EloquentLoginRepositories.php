<?php

namespace App\Repositories\Auth\Login;

/**
 * import component 
 */

use Illuminate\Support\Facades\Auth;

/**
 * import traits
 */

use App\Traits\JwtResponse;
use App\Traits\Response;
use App\Traits\Message;

/**
 * import interface
 */

use App\Repositories\Auth\Login\LoginRepositories;
use App\Repositories\Log\LogRepositories;

class EloquentLoginRepositories implements LoginRepositories
{
    use JwtResponse, Response, Message;

    private $logRepositories;

    public function __construct(
        LogRepositories $logRepositories
    ) {
        $this->logRepositories = $logRepositories;
    }

    /**
     * login process
     */
    public function login($request)
    {
        try {

            $usernamePassword = array_merge(['password' => $request['password']], ['username' => $request['username']]);
            $kodePassword = array_merge(['password' => $request['password']], ['kode' => $request['username']]);
            $token = Auth::attempt($usernamePassword) ? Auth::attempt($usernamePassword) : Auth::attempt($kodePassword);
            if (!$token) {
                $message = $this->outputLogMessage('login fail', $request['username']);
                $uuidUser = null;
                $response = $this->error('Invalid credentials');
            } else {
                $tokenData = $this->tokenResponse($token);
                $message = $this->outputLogMessage('login success', $request['username']);
                $uuidUser = $tokenData['user']['uuid_user'];
                $response = array_merge(
                    $this->success('ok'),
                    $tokenData
                );
            }

            /**
             * save log
             */
            $this->logRepositories->saveLog($message['action'], $message['message'], $uuidUser, null);
        } catch (\Throwable $e) {
            $response = $this->error($e->getMessage());
        }

        return $response;
    }
}

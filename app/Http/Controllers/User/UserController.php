<?php

namespace App\Http\Controllers\User;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\UpdatePasswordRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\User\UserRepositories;
use App\Repositories\Log\LogRepositories;

class UserController extends Controller
{
    use Message;

    private $signature;
    private $request;
    private $logRepositories;
    private $userRepositories;

    public function __construct(
        Request $request,
        UserRepositories $userRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->userRepositories = $userRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->request = $request;
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * all record data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->userRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'user');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid slider
     */
    public function get($param)
    {
        /**
         * process to database
         */
        $response = $this->userRepositories->get($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'user', 'by :' . $param);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store data
     */
    public function store(
        StoreRequest $storeRequest
    ) {
        /**
         * requesting data
         */
        $storeRequest = $storeRequest->all();

        /**
         * process begin
         */
        $response = $this->userRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'user ' . $storeRequest['name']);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update data
     */
    public function update(
        $uuidUser,
        UpdateRequest $updateRequest
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidUser, json_encode($updateRequest), 'user');

        /**
         * process begin
         */
        $response = $this->userRepositories->update($uuidUser, $updateRequest);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * delete data
     */
    public function delete($uuidUser)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidUser, null, 'user');

        /**
         * process begin
         */
        $response = $this->userRepositories->delete($uuidUser);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /** 
         * response
         */
        return response()->json($response);
    }

    /**
     * update password
     */
    public function updatePassword(
        $uuidUser,
        UpdatePasswordRequest $updateRequest
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidUser, json_encode($updateRequest), 'user');

        /**
         * process begin
         */
        $response = $this->userRepositories->updatePassword($uuidUser, $updateRequest);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * search data
     */
    public function search()
    {
        /**
         * load data from repositories
         */
        $response = $this->userRepositories->search($this->request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'user');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

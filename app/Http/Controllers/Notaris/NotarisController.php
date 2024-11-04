<?php

namespace App\Http\Controllers\Notaris;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import request
 */
use Illuminate\Http\Request;
use App\Http\Requests\Notaris\UpdateRequest;
use App\Http\Requests\Notaris\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Notaris\NotarisRepositories;
use App\Repositories\Auth\Register\RegisterRepositories;
use App\Repositories\Log\LogRepositories;

class NotarisController extends Controller
{
    use Message;

    private $signature;
    private $request;
    private $bidangRepositories;
    private $logRepositories;
    private $notarisRepositories;
    private $registerRepositories;

    public function __construct(
        Request $request,
        NotarisRepositories $notarisRepositories,
        LogRepositories $logRepositories,
        RegisterRepositories $registerRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->notarisRepositories = $notarisRepositories;
        $this->logRepositories = $logRepositories;
        $this->registerRepositories = $registerRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
        $this->request = $request;
    }

    /**
     * all record notaris
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->notarisRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'notaris');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * verifikasi notaris
     */
    public function verifikasi($uuidUser)
    {
        /**
         * load data from repositories
         */
        $response = $this->notarisRepositories->verifikasi($uuidUser);

        /**
         * save log
         */
        $log = $this->outputLogMessage('update', $uuidUser, 'status verifikasi', 'user');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid slider
     */
    public function get($uuidUser)
    {
        /**
         * process to database
         */
        $response = $this->notarisRepositories->get($uuidUser);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'user', 'by uuid:' . $uuidUser);
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
        $response = $this->notarisRepositories->update($uuidUser, $updateRequest);

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
        $response = $this->notarisRepositories->delete($uuidUser);

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
        $response = $this->registerRepositories->storeNotaris($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'notaris ' . $storeRequest['name']);
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
        $response = $this->notarisRepositories->search($this->request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'notaris');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

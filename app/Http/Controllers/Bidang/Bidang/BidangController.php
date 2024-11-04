<?php

namespace App\Http\Controllers\Bidang\Bidang;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Bidang\Bidang\StoreRequest;
use App\Http\Requests\Bidang\Bidang\UpdateRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Bidang\Bidang\BidangRepositories;
use App\Repositories\Log\LogRepositories;

class BidangController extends Controller
{
    use Message;

    private $signature;
    private $bidangRepositories;
    private $logRepositories;

    public function __construct(
        Request $request,
        BidangRepositories $bidangRepositories,
        LogRepositories $logRepositories
    ) {

        /**
         * initialize repositories
         */
        $this->bidangRepositories = $bidangRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
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
        $response = $this->bidangRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'bidang');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid bidang
     */
    public function get($uuidBidang)
    {
        /**
         * process to database
         */
        $response = $this->bidangRepositories->get($uuidBidang);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'bidang', 'uuid bidang :' . $uuidBidang);
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
        $response = $this->bidangRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'bidang ' . $storeRequest['nama_bidang']);
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
        $uuidBidang,
        UpdateRequest $updateRequest
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidBidang, 'bidang ' . $updateRequest['nama_bidang'], 'bidang');

        /**
         * process begin
         */
        $response = $this->bidangRepositories->update($updateRequest, $uuidBidang);

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
    public function delete($uuidBidang)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidBidang, null, 'bidang');

        /**
         * process begin
         */
        $response = $this->bidangRepositories->delete($uuidBidang);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /** 
         * response
         */
        return response()->json($response);
    }
}

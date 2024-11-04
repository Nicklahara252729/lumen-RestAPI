<?php

namespace App\Http\Controllers\Bidang\SubBidang;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Bidang\SubBidang\StoreRequest;
use App\Http\Requests\Bidang\SubBidang\UpdateRequest;

/**
 * import
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Bidang\SubBidang\SubBidangRepositories;
use App\Repositories\Log\LogRepositories;

class SubBidangController extends Controller
{
    use Message;

    private $signature;
    private $subBidangRepositories;
    private $logRepositories;

    public function __construct(
        Request $request,
        SubBidangRepositories $subBidangRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->subBidangRepositories = $subBidangRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * get all record data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->subBidangRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'sub bidang');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid sub bidang
     */
    public function get($uuidSubBidang)
    {
        /**
         * process to database
         */
        $response = $this->subBidangRepositories->get($uuidSubBidang);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'sub bidang', 'uuid sub bidang :' . $uuidSubBidang);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid bidang
     */
    public function getByBidang($param)
    {
        /**
         * process to database
         */
        $response = $this->subBidangRepositories->getByBidang($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'sub bidang by ' . $param);
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
        $response = $this->subBidangRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'sub bidang ' . $storeRequest['nama_sub_bidang']);
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
        UpdateRequest $updateRequest,
        $uuidSubBidang
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidSubBidang, 'sub bidang ' . $updateRequest['nama_sub_bidang'], 'sub bidang');

        /**
         * process begin
         */
        $response = $this->subBidangRepositories->update($updateRequest, $uuidSubBidang);

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
    public function delete($uuidSubBidang)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidSubBidang, null, 'sub bidang');

        /**
         * process begin
         */
        $response = $this->subBidangRepositories->delete($uuidSubBidang);

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

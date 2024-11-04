<?php

namespace App\Http\Controllers\PbbMinimal;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\PbbMinimal\StoreRequest;
use App\Http\Requests\PbbMinimal\UpdateRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\PbbMinimal\PbbMinimalRepositories;
use App\Repositories\Log\LogRepositories;

class PbbMinimalController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $pbbMinimalRepositories;

    public function __construct(
        Request $request,
        PbbMinimalRepositories $pbbMinimalRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->pbbMinimalRepositories = $pbbMinimalRepositories;
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
        $response = $this->pbbMinimalRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'pbb minimal');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid slider
     */
    public function get($thnPbbMinimal)
    {
        /**
         * process to database
         */
        $response = $this->pbbMinimalRepositories->get($thnPbbMinimal);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'pbb minimal', 'tahun ' . $thnPbbMinimal);
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
        $response = $this->pbbMinimalRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'pbb minimal ' . json_encode($storeRequest));
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
        $thnPbbMinimal
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $thnPbbMinimal, 'pbb minimal ' . json_encode($updateRequest), 'pbb minimal');

        /**
         * process begin
         */
        $response = $this->pbbMinimalRepositories->update(
            $updateRequest,
            $thnPbbMinimal
        );

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
    public function delete($thnPbbMinimal)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $thnPbbMinimal, null, 'pbb minimal');

        /**
         * process begin
         */
        $response = $this->pbbMinimalRepositories->delete($thnPbbMinimal);

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

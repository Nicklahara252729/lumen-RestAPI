<?php

namespace App\Http\Controllers\MasterData\Npoptkp;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\MasterData\Npoptkp\StoreRequest;
use App\Http\Requests\MasterData\Npoptkp\UpdateRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\MasterData\Npoptkp\NpoptkpRepositories;
use App\Repositories\Log\LogRepositories;

class NpoptkpController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $npoptkpRepositories;

    public function __construct(
        Request $request,
        NpoptkpRepositories $npoptkpRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->npoptkpRepositories = $npoptkpRepositories;
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
        $response = $this->npoptkpRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'npoptkp');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid npoptkp
     */
    public function get($uuidNpoptkp)
    {
        /**
         * process to database
         */
        $response = $this->npoptkpRepositories->get($uuidNpoptkp);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data','npoptkp', 'uuid npoptkp :' . $uuidNpoptkp);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store data
     */
    public function store(StoreRequest $storeRequest)
    {
        /**
         * requesting data
         */
        $storeRequest = $storeRequest->all();

        /**
         * process begin
         */
        $response = $this->npoptkpRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'npoptkp ' .'NPOPTKP tahun '. $storeRequest['tahun']);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update data
     */
    public function update(UpdateRequest $updateRequest, $uuidNpoptkp)
    {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidNpoptkp, 'npoptkp ' . 'NPOPTKP tahun '.$updateRequest['tahun'], 'npoptkp');

        /**
         * process begin
         */
        $response = $this->npoptkpRepositories->update($updateRequest, $uuidNpoptkp);

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
    public function delete($uuidNpoptkp)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidNpoptkp, null, 'npoptkp');

        /**
         * process begin
         */
        $response = $this->npoptkpRepositories->delete($uuidNpoptkp);

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

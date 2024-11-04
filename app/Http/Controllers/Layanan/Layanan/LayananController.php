<?php

namespace App\Http\Controllers\Layanan\Layanan;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Layanan\Layanan\StoreRequest;
use App\Http\Requests\Layanan\Layanan\UpdateRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Layanan\Layanan\LayananRepositories;
use App\Repositories\Log\LogRepositories;

class LayananController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $layananRepositories;

    public function __construct(
        Request $request,
        LayananRepositories $layananRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->layananRepositories = $layananRepositories;
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
        $response = $this->layananRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'layanan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid bidang
     */
    public function get($uuidLayanan)
    {
        /**
         * process to database
         */
        $response = $this->layananRepositories->get($uuidLayanan);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'layanan', 'uuid layanan :' . $uuidLayanan);
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
        $response = $this->layananRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'layanan ' . $storeRequest['layanan']);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update data
     */
    public function update(UpdateRequest $updateRequest, $uuidLayanan)
    {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidLayanan, 'layanan ' . $updateRequest['layanan'], 'layanan');

        /**
         * process begin
         */
        $response = $this->layananRepositories->update($updateRequest, $uuidLayanan);

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
    public function delete($uuidLayanan)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidLayanan, null, 'layanan');

        /**
         * process begin
         */
        $response = $this->layananRepositories->delete($uuidLayanan);

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

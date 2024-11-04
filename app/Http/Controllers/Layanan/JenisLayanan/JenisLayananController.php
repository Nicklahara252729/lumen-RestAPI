<?php

namespace App\Http\Controllers\Layanan\JenisLayanan;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Layanan\JenisLayanan\StoreRequest;
use App\Http\Requests\Layanan\JenisLayanan\UpdateRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Layanan\JenisLayanan\JenisLayananRepositories;
use App\Repositories\Log\LogRepositories;

class JenisLayananController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $jenisLayananRepositories;

    public function __construct(
        Request $request,
        JenisLayananRepositories $jenisLayananRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->jenisLayananRepositories = $jenisLayananRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * get all record data
     */
    public function data($status)
    {
        /**
         * load data from repositories
         */
        $response = $this->jenisLayananRepositories->data($status);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'jenis layanan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid sub bidang
     */
    public function get($uuidJenisLayanan)
    {
        /**
         * process to database
         */
        $response = $this->jenisLayananRepositories->get($uuidJenisLayanan);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data','jenis layanan', 'uuid jenis layanan :' . $uuidJenisLayanan);
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
        $response = $this->jenisLayananRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'jenis layanan ' . $storeRequest['jenis_layanan']);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update data
     */
    public function update(UpdateRequest $updateRequest, $uuidJenisLayanan)
    {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidJenisLayanan, 'jenis layanan ' . $updateRequest['jenis_layanan'], 'jenis layanan');

        /**
         * process begin
         */
        $response = $this->jenisLayananRepositories->update($updateRequest, $uuidJenisLayanan);

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
    public function delete($uuidJenisLayanan)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidJenisLayanan, null, 'jenis layanan');

        /**
         * process begin
         */
        $response = $this->jenisLayananRepositories->delete($uuidJenisLayanan);

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

<?php

namespace App\Http\Controllers\JenisPerolehan;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\JenisPerolehan\StoreRequest;
use App\Http\Requests\JenisPerolehan\UpdateRequest;
use App\Http\Requests\JenisPerolehan\UpdateStatusRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\JenisPerolehan\JenisPerolehanRepositories;
use App\Repositories\Log\LogRepositories;

class JenisPerolehanController extends Controller
{
    use Message;

    private $signature;
    private $jenisPerolehanRepositories;
    private $logRepositories;

    public function __construct(
        Request $request,
        JenisPerolehanRepositories $jenisPerolehanRepositories,
        LogRepositories $logRepositories
    ) {

        /**
         * initialize repositories
         */
        $this->jenisPerolehanRepositories = $jenisPerolehanRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * all record data
     */
    public function data($pelayanan)
    {
        /**
         * load data from repositories
         */
        $response = $this->jenisPerolehanRepositories->data($pelayanan);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'jenis perolehan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid jenis perolehan
     */
    public function get($uuidJenisPerolehan)
    {
        /**
         * process to database
         */
        $response = $this->jenisPerolehanRepositories->get($uuidJenisPerolehan);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'jenis perolehan', 'uuid jenis perolehan :' . $uuidJenisPerolehan);
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
        $response = $this->jenisPerolehanRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'jenis perolehan ' . $storeRequest['jenis_perolehan']);
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
        $uuidJenisPerolehan,
        UpdateRequest $updateRequest
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidJenisPerolehan, 'jenis perolehan ' . $updateRequest['jenis_perolehan'], 'jenis perolehan');

        /**
         * process begin
         */
        $response = $this->jenisPerolehanRepositories->update($updateRequest, $uuidJenisPerolehan);

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
     * update data status
     */
    public function updateStatus(
        $uuidJenisPerolehan,
        UpdateStatusRequest $updateStatusRequest
    ) {
        /**
         * requesting data
         */
        $updateStatusRequest = $updateStatusRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidJenisPerolehan, 'status jenis perolehan ' . $updateStatusRequest['status'], 'jenis perolehan');

        /**
         * process begin
         */
        $response = $this->jenisPerolehanRepositories->update($updateStatusRequest, $uuidJenisPerolehan);

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
    public function delete($uuidJenisPerolehan)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidJenisPerolehan, null, 'jenis perolehan');

        /**
         * process begin
         */
        $response = $this->jenisPerolehanRepositories->delete($uuidJenisPerolehan);

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

<?php

namespace App\Http\Controllers\Pelayanan\Pbb\Pendaftaran;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Pelayanan\Pbb\Pendaftaran\UpdateStatusVerifikasiRequest;
use App\Http\Requests\Pelayanan\Pbb\Pendaftaran\StoreRequest;
use App\Http\Requests\Pelayanan\Pbb\Pendaftaran\StoreLspopRequest;
use App\Http\Requests\Pelayanan\Pbb\Pendaftaran\UpdateRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import export
 */

use App\Exports\Pelayanan\PendaftaranExport;

/**
 * import repositories
 */

use App\Repositories\Pelayanan\Pbb\Pendaftaran\PendaftaranRepositories;
use App\Repositories\Log\LogRepositories;

class PendaftaranController extends Controller
{
    use Message;

    private $signature;
    private $request;
    private $logRepositories;
    private $pendaftaranRepositories;

    public function __construct(
        Request $request,
        PendaftaranRepositories $pendaftaranRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->pendaftaranRepositories = $pendaftaranRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * save data
     */
    public function store(StoreRequest $storeRequest)
    {
        /**
         * load data from repositories
         */
        $response = $this->pendaftaranRepositories->store($storeRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'pelayanan value ' . json_encode($storeRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * save data lspop
     */
    public function storeLspop(StoreLspopRequest $storeLspopRequest)
    {
        /**
         * load data from repositories
         */
        $response = $this->pendaftaranRepositories->storeLspop($storeLspopRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'lspop bangunan value ' . json_encode($storeLspopRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get single data
     */
    public function get($param)
    {
        /**
         * load data from repositories
         */
        $response = $this->pendaftaranRepositories->get($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'pelayanan', 'parameter :' . $param);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update status verifikasi
     */
    public function updateStatusVerifikasi(
        $uuidPelayanan,
        UpdateStatusVerifikasiRequest $request
    ) {
        /**
         * load data from repositories
         */
        $response = $this->pendaftaranRepositories->updateStatusVerifikasi(
            $request->all(),
            $uuidPelayanan
        );

        /**
         * save log
         */
        $log = $this->outputLogMessage('update', $uuidPelayanan, json_encode($request->all()), 'status pelayanan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * delete pelayanan
     */
    public function delete($uuidPelayanan, $uuidUser)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidPelayanan, null, 'pelayanan');

        /**
         * process begin
         */
        $response = $this->pendaftaranRepositories->delete($uuidPelayanan, $uuidUser);

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
     * update data
     */
    public function update(
        $uuidPelayanan,
        UpdateRequest $updateRequest
    ) {
        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidPelayanan, json_encode($updateRequest->all()), 'pelayanan');

        /**
         * load data from repositories
         */
        $response = $this->pendaftaranRepositories->update(
            $uuidPelayanan,
            $updateRequest->all()
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
     * export data
     */
    public function export()
    {
        /**
         * save log
         */
        $log = $this->outputLogMessage('export', 'pelayanan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        return new PendaftaranExport();
    }
}

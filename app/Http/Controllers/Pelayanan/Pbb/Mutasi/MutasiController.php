<?php

namespace App\Http\Controllers\Pelayanan\Pbb\Mutasi;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use App\Http\Requests\Pelayanan\Pbb\Mutasi\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Pelayanan\Pbb\Mutasi\MutasiRepositories;
use App\Repositories\Log\LogRepositories;

class MutasiController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $mutasiRepositories;

    public function __construct(
        MutasiRepositories $mutasiRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->mutasiRepositories = $mutasiRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = authAttribute()['id'];
    }

    /**
     * save mutasi
     */
    public function store(StoreRequest $storeRequest) {
        /**
         * load data from repositories
         */
        $storeRequest = $storeRequest->all();
        $response = $this->mutasiRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'mutasi value ' . json_encode($storeRequest));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

<?php

namespace App\Http\Controllers\Pelayanan\Pbb\PetaObjekPajak;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use App\Http\Requests\Pelayanan\Pbb\PetaObjekPajak\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Pelayanan\Pbb\PetaObjekPajak\PetaObjekPajakRepositories;
use App\Repositories\Log\LogRepositories;

class PetaObjekPajakController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $petaObjekPajakRepositories;

    public function __construct(
        PetaObjekPajakRepositories $petaObjekPajakRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->petaObjekPajakRepositories = $petaObjekPajakRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = authAttribute()['id'];
    }

    /**
     * save data
     */
    public function store(StoreRequest $storeRequest)
    {
        /**
         * load data from repositories
         */
        $response = $this->petaObjekPajakRepositories->store($storeRequest->all());

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
     * get all data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->petaObjekPajakRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'peta objek pajak');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}
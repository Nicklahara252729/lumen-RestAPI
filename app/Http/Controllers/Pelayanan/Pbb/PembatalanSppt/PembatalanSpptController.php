<?php

namespace App\Http\Controllers\Pelayanan\Pbb\PembatalanSppt;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */
use Illuminate\Http\Request;
use App\Http\Requests\Pelayanan\Pbb\PembatalanSppt\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Pelayanan\Pbb\PembatalanSppt\PembatalanSpptRepositories;
use App\Repositories\Log\LogRepositories;

class PembatalanSpptController extends Controller
{
    use Message;

    private $request;
    private $signature;
    private $logRepositories;
    private $pembatalanSpptRepositories;

    public function __construct(
        Request $request,
        PembatalanSpptRepositories $pembatalanSpptRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->pembatalanSpptRepositories = $pembatalanSpptRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->request = $request;
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * store data
     */
    public function store(StoreRequest $storeRequest)
    {
        /**
         * load data from repositories
         */
        $response = $this->pembatalanSpptRepositories->store($storeRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'pembatalan sppt value ' . json_encode($storeRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

}

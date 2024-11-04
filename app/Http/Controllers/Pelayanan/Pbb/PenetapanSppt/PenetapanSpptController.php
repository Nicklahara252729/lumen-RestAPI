<?php

namespace App\Http\Controllers\Pelayanan\Pbb\PenetapanSppt;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */
use Illuminate\Http\Request;
use App\Http\Requests\Pelayanan\Pbb\PenetapanSppt\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Pelayanan\Pbb\PenetapanSppt\PenetapanSpptRepositories;
use App\Repositories\Log\LogRepositories;

class PenetapanSpptController extends Controller
{
    use Message;

    private $request;
    private $signature;
    private $logRepositories;
    private $penetapanSpptRepositories;

    public function __construct(
        Request $request,
        PenetapanSpptRepositories $penetapanSpptRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->penetapanSpptRepositories = $penetapanSpptRepositories;
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
        $response = $this->penetapanSpptRepositories->store($storeRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'penetapan sppt value ' . json_encode($storeRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

}

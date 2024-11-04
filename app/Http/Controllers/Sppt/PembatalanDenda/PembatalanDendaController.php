<?php

namespace App\Http\Controllers\Sppt\PembatalanDenda;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */
use Illuminate\Http\Request;
use App\Http\Requests\Sppt\PembatalanDenda\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Sppt\PembatalanDenda\PembatalanDendaRepositories;
use App\Repositories\Log\LogRepositories;

class PembatalanDendaController extends Controller
{
    use Message;

    private $request;
    private $signature;
    private $logRepositories;
    private $pembatalanDendaRepositories;

    public function __construct(
        Request $request,
        PembatalanDendaRepositories $pembatalanDendaRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->pembatalanDendaRepositories = $pembatalanDendaRepositories;
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
        $response = $this->pembatalanDendaRepositories->store($storeRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'pembatalan denda value ' . json_encode($storeRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

}

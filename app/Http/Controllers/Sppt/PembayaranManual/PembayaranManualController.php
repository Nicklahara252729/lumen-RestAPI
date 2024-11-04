<?php

namespace App\Http\Controllers\Sppt\PembayaranManual;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Sppt\PembayaranManual\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Sppt\PembayaranManual\PembayaranManualRepositories;
use App\Repositories\Log\LogRepositories;

class PembayaranManualController extends Controller
{
    use Message;

    private $request;
    private $signature;
    private $logRepositories;
    private $pembayaranManualRepositories;

    public function __construct(
        Request $request,
        PembayaranManualRepositories $pembayaranManualRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->pembayaranManualRepositories = $pembayaranManualRepositories;
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
        $response = $this->pembayaranManualRepositories->store($storeRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'pembayaran manual value ' . json_encode($storeRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->pembayaranManualRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'pembayaran manual');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

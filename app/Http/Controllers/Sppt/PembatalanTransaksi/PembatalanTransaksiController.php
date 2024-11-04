<?php

namespace App\Http\Controllers\Sppt\PembatalanTransaksi;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */
use Illuminate\Http\Request;
use App\Http\Requests\Sppt\PembatalanTransaksi\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Sppt\PembatalanTransaksi\PembatalanTransaksiRepositories;
use App\Repositories\Log\LogRepositories;

class PembatalanTransaksiController extends Controller
{
    use Message;

    private $request;
    private $signature;
    private $logRepositories;
    private $pembatalanTransaksiRepositories;

    public function __construct(
        Request $request,
        PembatalanTransaksiRepositories $pembatalanTransaksiRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->pembatalanTransaksiRepositories = $pembatalanTransaksiRepositories;
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
        $response = $this->pembatalanTransaksiRepositories->store($storeRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'pembatalan transaksi value ' . json_encode($storeRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

}

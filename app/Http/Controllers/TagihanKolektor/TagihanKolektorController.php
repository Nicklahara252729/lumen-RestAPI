<?php

namespace App\Http\Controllers\TagihanKolektor;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import request
 */

use Illuminate\Http\Request;
use App\Http\Requests\TagihanKolektor\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\TagihanKolektor\TagihanKolektorRepositories;
use App\Repositories\Log\LogRepositories;

class TagihanKolektorController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $tagihanKolektorRepositories;

    public function __construct(
        Request $request,
        TagihanKolektorRepositories $tagihanKolektorRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->tagihanKolektorRepositories = $tagihanKolektorRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * all record data
     */
    public function data($pageSize = null)
    {

        /**
         * load data from repositories
         */
        $response = $this->tagihanKolektorRepositories->data($pageSize);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'tagihan kolektor');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get single data by nop
     */
    public function get($nop)
    {
        /**
         * process to database
         */
        $response = $this->tagihanKolektorRepositories->get($nop);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'tagihan kolektor', 'by nop :' . $nop);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * save data
     */
    public function store(StoreRequest $storeRequest)
    {
        /**
         * load data from repositories
         */
        $response = $this->tagihanKolektorRepositories->store($storeRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'tagihan value ' . json_encode($storeRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

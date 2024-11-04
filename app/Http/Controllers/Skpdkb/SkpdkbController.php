<?php

namespace App\Http\Controllers\Skpdkb;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Skpdkb\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Skpdkb\SkpdkbRepositories;
use App\Repositories\Log\LogRepositories;

class SkpdkbController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $skpdkbRepositories;

    public function __construct(
        Request $request,
        SkpdkbRepositories $skpdkbRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->skpdkbRepositories = $skpdkbRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * all record data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->skpdkbRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'SKPDKB');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store data
     */
    public function store(StoreRequest $storeRequest)
    {
        /**
         * load data from repositories
         */
        $response = $this->skpdkbRepositories->store($storeRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'SKPDKB value ' . json_encode($storeRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

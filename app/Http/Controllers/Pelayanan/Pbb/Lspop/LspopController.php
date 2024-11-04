<?php

namespace App\Http\Controllers\Pelayanan\Pbb\Lspop;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Pelayanan\Pbb\Lspop\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Pelayanan\Pbb\Lspop\LspopRepositories;
use App\Repositories\Log\LogRepositories;

class LspopController extends Controller
{
    use Message;

    private $request;
    private $signature;
    private $logRepositories;
    private $lspopRepositories;

    public function __construct(
        Request $request,
        LspopRepositories $lspopRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->lspopRepositories = $lspopRepositories;
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
        $request = $storeRequest->all();
        $response = $this->lspopRepositories->store($request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'LSPOP value ' . json_encode($request));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

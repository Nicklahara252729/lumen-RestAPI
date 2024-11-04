<?php

namespace App\Http\Controllers\Pelayanan\Pbb\GabungNop;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Pelayanan\Pbb\GabungNop\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Pelayanan\Pbb\GabungNop\GabungNopRepositories;
use App\Repositories\Log\LogRepositories;

class GabungNopController extends Controller
{
    use Message;

    private $signature;
    private $request;
    private $logRepositories;
    private $gabungNopRepositories;

    public function __construct(
        Request $request,
        GabungNopRepositories $gabungNopRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->gabungNopRepositories = $gabungNopRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * save gabung nop
     */
    public function store(StoreRequest $request)
    {
        /**
         * load data from repositories
         */
        $response = $this->gabungNopRepositories->store($request->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'gabung nop value ' . json_encode($request->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

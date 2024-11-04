<?php

namespace App\Http\Controllers\Log\Bphtb;

/**
 * import component
 */

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Log\LogRepositories;

class LogBphtbController extends Controller
{
    use Message;

    private $request;
    private $signature;
    private $logRepositories;

    public function __construct(
        Request $request,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */

        $this->logRepositories = $logRepositories;

        /**
         * initialize static value
         */
        $this->request = $request;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * listing data log data bphtb by noneg
     */
    public function data($noRegistrasi)
    {
        /**
         * load data from repositories
         */
        $response = $this->logRepositories->LogBphtb($noRegistrasi);

        /**
         * response
         */
        return response()->json($response);
    }
}

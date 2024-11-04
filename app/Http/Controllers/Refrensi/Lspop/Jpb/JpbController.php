<?php

namespace App\Http\Controllers\Refrensi\Lspop\Jpb;

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

use App\Repositories\Refrensi\Lspop\Jpb\JpbRepositories;
use App\Repositories\Log\LogRepositories;

class JpbController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $request;
    private $jpbRepositories;

    public function __construct(
        Request $request,
        JpbRepositories $jpbRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->jpbRepositories = $jpbRepositories;
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
        $response = $this->jpbRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'jpb');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

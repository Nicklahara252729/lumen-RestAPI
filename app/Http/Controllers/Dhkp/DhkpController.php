<?php

namespace App\Http\Controllers\Dhkp;

/**
 * import collection 
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

use App\Repositories\Dhkp\DhkpRepositories;
use App\Repositories\Log\LogRepositories;

class DhkpController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $dhkpRepositories;
    private $request;

    public function __construct(
        Request $request,
        DhkpRepositories $dhkpRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->dhkpRepositories = $dhkpRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->dhkpRepositories->data($this->request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'dhkp', json_encode($this->request->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }    
}

<?php

namespace App\Http\Controllers\Refrensi\KelasBumi;

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

use App\Repositories\Refrensi\KelasBumi\KelasBumiRepositories;
use App\Repositories\Log\LogRepositories;

class KelasBumiController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $request;
    private $kelasBumiRepositories;

    public function __construct(
        Request $request,
        KelasBumiRepositories $kelasBumiRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->kelasBumiRepositories = $kelasBumiRepositories;
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
        $response = $this->kelasBumiRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'kelas bumi');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

<?php

namespace App\Http\Controllers\Kaban;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import request
 */

use Illuminate\Http\Request;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Kaban\KabanRepositories;
use App\Repositories\Log\LogRepositories;

class KabanController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $kabanRepositories;

    public function __construct(
        Request $request,
        KabanRepositories $kabanRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->kabanRepositories = $kabanRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * data from reklame and pat
     */
    public function sts()
    {

        /**
         * load data from repositories
         */
        $response = $this->kabanRepositories->sts();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'top 10 STS');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * top 10 sts from reklame and pat
     */
    public function stsTertinggi()
    {

        /**
         * load data from repositories
         */
        $response = $this->kabanRepositories->stsTertinggi();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'top 10 STS');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get single data sts by no sts
     */
    public function detailSts($noSts)
    {
        /**
         * process to database
         */
        $response = $this->kabanRepositories->detailSts($noSts);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'sts history', 'by no sts:' . $noSts);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

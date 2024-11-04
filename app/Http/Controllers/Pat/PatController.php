<?php

namespace App\Http\Controllers\Pat;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Pat\VerifikasiRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Pat\PatRepositories;
use App\Repositories\Log\LogRepositories;

class PatController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $patRepositories;
    private $request;

    public function __construct(
        Request $request,
        PatRepositories $patRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->patRepositories = $patRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * data
     */
    public function data($status)
    {
        /**
         * load data from repositories
         */
        $response = $this->patRepositories->data($status);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'pat');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update status verifikasi
     */
    public function verifikasi(
        VerifikasiRequest $request,
        $idPat
    ) {
        /**
         * load data from repositories
         */
        $request = $request->all();
        $response = $this->patRepositories->verifikasi($request, $idPat);

        /**
         * save log
         */
        $log = $this->outputLogMessage('update', $idPat, json_encode($request), 'status pat');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * sts tertinggi
     */
    public function stsTertinggi()
    {
        /**
         * load data from repositories
         */
        $response = $this->patRepositories->stsTertinggi();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'sts tertinggi pat');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * data sts
     */
    public function dataSts()
    {
        /**
         * load data from repositories
         */
        $response = $this->patRepositories->dataSts();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'sts pat');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

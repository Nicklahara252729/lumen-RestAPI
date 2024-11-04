<?php

namespace App\Http\Controllers\Reklame;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Reklame\VerifikasiRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Reklame\ReklameRepositories;
use App\Repositories\Log\LogRepositories;

class ReklameController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $reklameRepositories;
    private $request;

    public function __construct(
        Request $request,
        ReklameRepositories $reklameRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->reklameRepositories = $reklameRepositories;
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
        $response = $this->reklameRepositories->data($status);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'reklame');
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
        $idReklame
    ) {
        /**
         * load data from repositories
         */
        $request = $request->all();
        $response = $this->reklameRepositories->verifikasi($request, $idReklame);

        /**
         * save log
         */
        $log = $this->outputLogMessage('update', $idReklame, json_encode($request), 'status reklame');
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
        $response = $this->reklameRepositories->stsTertinggi();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'reklame');
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
        $response = $this->reklameRepositories->dataSts();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'reklame');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

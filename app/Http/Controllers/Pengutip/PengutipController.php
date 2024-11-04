<?php

namespace App\Http\Controllers\Pengutip;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Pengutip\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Pengutip\PengutipRepositories;
use App\Repositories\Log\LogRepositories;

class PengutipController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $pengutipRepositories;
    private $request;

    public function __construct(
        Request $request,
        PengutipRepositories $pengutipRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->pengutipRepositories = $pengutipRepositories;
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
        $response = $this->pengutipRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'pengutip');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * data restoran
     */
    public function dataRestoran()
    {
        /**
         * load data from repositories
         */
        $response = $this->pengutipRepositories->dataRestoran();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'pengutip restoran');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * 
     * autocomplete
     */
    public function autocomplete($nopd)
    {
        /**
         * load data from repositories
         */
        $response = $this->pengutipRepositories->autocomplete($nopd);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'pengutip', 'nopd : ' . $nopd);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store pengutip
     */
    public function store(
        StoreRequest $request
    ) {
        /**
         * load data from repositories
         */
        $request = $request->all();
        $response = $this->pengutipRepositories->store($request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'pengutip ' . json_encode($request));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

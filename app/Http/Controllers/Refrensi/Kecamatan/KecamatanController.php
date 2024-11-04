<?php

namespace App\Http\Controllers\Refrensi\Kecamatan;

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

use App\Repositories\Refrensi\Kecamatan\KecamatanRepositories;
use App\Repositories\Log\LogRepositories;

class KecamatanController extends Controller
{

    use Message;

    private $signature;
    private $logRepositories;
    private $request;
    private $kecamatanRepositories;

    public function __construct(
        Request $request,
        KecamatanRepositories $kecamatanRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->kecamatanRepositories = $kecamatanRepositories;
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
        $response = $this->kecamatanRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'referensi kecamatan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * all record data for PAD
     */
    public function dataPAD()
    {
        /**
         * load data from repositories
         */
        $response = $this->kecamatanRepositories->dataPAD();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'kecamatan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

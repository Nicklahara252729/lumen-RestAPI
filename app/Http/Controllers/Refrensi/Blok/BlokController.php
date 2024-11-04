<?php

namespace App\Http\Controllers\Refrensi\Blok;

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

use App\Repositories\Refrensi\Blok\BlokRepositories;
use App\Repositories\Log\LogRepositories;

class BlokController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $request;
    private $blokRepositories;

    public function __construct(
        Request $request,
        BlokRepositories $blokRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->blokRepositories = $blokRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * all record data
     */
    public function getAll()
    {
        /**
         * load data from repositories
         */
        $response = $this->blokRepositories->getAll();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'blok');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * all record data by param
     */
    public function data($kdKecamatan, $kdKelurahan)
    {
        /**
         * load data from repositories
         */
        $response = $this->blokRepositories->data($kdKecamatan, $kdKelurahan);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'blok', 'blok by kode kecamatan ' . $kdKecamatan . ' dan kode kelurahan ' . $kdKelurahan);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

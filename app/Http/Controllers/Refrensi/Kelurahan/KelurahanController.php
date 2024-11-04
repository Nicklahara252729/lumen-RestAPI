<?php

namespace App\Http\Controllers\Refrensi\Kelurahan;

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

use App\Repositories\Refrensi\Kelurahan\KelurahanRepositories;
use App\Repositories\Log\LogRepositories;

class KelurahanController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $request;
    private $kelurahanRepositories;

    public function __construct(
        Request $request,
        KelurahanRepositories $kelurahanRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->kelurahanRepositories = $kelurahanRepositories;
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
        $response = $this->kelurahanRepositories->getAll();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'referensi kelurahan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * all record data with param
     */
    public function data($kdKecamatan)
    {
        /**
         * load data from repositories
         */
        $response = $this->kelurahanRepositories->data($kdKecamatan);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'referensi kelurahan', 'referensi kelurahan by kode kecamatan :' . $kdKecamatan);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * all record data with param for PAD
     */
    public function dataPAD($kecamatanId)
    {
        /**
         * load data from repositories
         */
        $response = $this->kelurahanRepositories->dataPAD($kecamatanId);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'referensi kelurahan', 'referensi kelurahan by id kecamatan :' . $kecamatanId);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

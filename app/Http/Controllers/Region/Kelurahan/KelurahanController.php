<?php

namespace App\Http\Controllers\Region\Kelurahan;

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

use App\Repositories\Region\Kelurahan\KelurahanRepositories;
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
    public function data($idKecamatan)
    {
        /**
         * load data from repositories
         */
        $response = $this->kelurahanRepositories->data($idKecamatan);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'kelurahan', 'id kecamatan :' . $idKecamatan);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get single data
     */
    public function get($param)
    {
        /**
         * process to database
         */
        $response = $this->kelurahanRepositories->get($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'kelurahan', 'by :' . str_replace('%20', ' ', $param));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

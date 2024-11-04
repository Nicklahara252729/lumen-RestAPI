<?php

namespace App\Http\Controllers\Region\Kabupaten;

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

use App\Repositories\Region\Kabupaten\KabupatenRepositories;
use App\Repositories\Log\LogRepositories;

class KabupatenController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $request;
    private $kabupatenRepositories;

    public function __construct(
        Request $request,
        KabupatenRepositories $kabupatenRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->kabupatenRepositories = $kabupatenRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * all record data
     */
    public function data($idProvinsi)
    {
        /**
         * load data from repositories
         */
        $response = $this->kabupatenRepositories->data($idProvinsi);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'kabupaten', 'id provinsi :' . $idProvinsi);
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
        $response = $this->kabupatenRepositories->get($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'kabupaten', 'by :' . str_replace('%20', ' ', $param));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

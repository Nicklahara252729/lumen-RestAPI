<?php

namespace App\Http\Controllers\Region\Provinsi;

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

use App\Repositories\Region\Provinsi\ProvinsiRepositories;
use App\Repositories\Log\LogRepositories;

class ProvinsiController extends Controller
{
    use Message;

    private $signature;
    private $request;
    private $logRepositories;
    private $provinsiRepositories;

    public function __construct(
        Request $request,
        ProvinsiRepositories $provinsiRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->provinsiRepositories = $provinsiRepositories;
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
        $response = $this->provinsiRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'provinsi');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid bidang
     */
    public function get($param)
    {
        /**
         * process to database
         */
        $response = $this->provinsiRepositories->get($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'provinsi', 'by :' . str_replace('%20', ' ', $param));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

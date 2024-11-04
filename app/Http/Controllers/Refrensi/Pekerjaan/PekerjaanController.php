<?php

namespace App\Http\Controllers\Refrensi\Pekerjaan;

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

use App\Repositories\Refrensi\Pekerjaan\PekerjaanRepositories;
use App\Repositories\Log\LogRepositories;

class PekerjaanController extends Controller
{

    use Message;

    private $signature;
    private $logRepositories;
    private $request;
    private $pekerjaanRepositories;

    public function __construct(
        Request $request,
        PekerjaanRepositories $pekerjaanRepositories,
        LogRepositories $logRepositories

    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->pekerjaanRepositories = $pekerjaanRepositories;
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
        $response = $this->pekerjaanRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'pekerjaan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by param
     */
    public function get($param)
    {
        /**
         * process to database
         */
        $response = $this->pekerjaanRepositories->get($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'blok', 'by :' . $param);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

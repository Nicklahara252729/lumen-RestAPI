<?php

namespace App\Http\Controllers\Region\Kecamatan;

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

use App\Repositories\Region\Kecamatan\KecamatanRepositories;
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
    public function data($idKabupaten)
    {
        /**
         * load data from repositories
         */
        $response = $this->kecamatanRepositories->data($idKabupaten);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'kecamatan', 'id kabupaten :' . $idKabupaten);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * single get data
     */
    public function get($param)
    {
        /**
         * process to database
         */
        $response = $this->kecamatanRepositories->get($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'kecamatan', 'by :' . str_replace('%20', ' ', $param));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

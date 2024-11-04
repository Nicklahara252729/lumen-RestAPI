<?php

namespace App\Http\Controllers\Refrensi\Lspop\Pekerjaan;

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

use App\Repositories\Refrensi\Lspop\Pekerjaan\PekerjaanRepositories;
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
    public function data($namaPekerjaan)
    {
        /**
         * load data from repositories
         */
        $namaPekerjaan = str_replace('%20', ' ', $namaPekerjaan);
        $response = $this->pekerjaanRepositories->data($namaPekerjaan);

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
}

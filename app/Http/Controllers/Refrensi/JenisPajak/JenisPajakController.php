<?php

namespace App\Http\Controllers\Refrensi\JenisPajak;

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

use App\Repositories\Refrensi\JenisPajak\JenisPajakRepositories;
use App\Repositories\Log\LogRepositories;

class JenisPajakController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $request;
    private $jenisPajakRepositories;

    public function __construct(
        Request $request,
        JenisPajakRepositories $jenisPajakRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->jenisPajakRepositories = $jenisPajakRepositories;
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
        $response = $this->jenisPajakRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'jenis pajak');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

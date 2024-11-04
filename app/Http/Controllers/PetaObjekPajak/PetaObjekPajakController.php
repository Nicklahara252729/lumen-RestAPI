<?php

namespace App\Http\Controllers\PetaObjekPajak;

/**
 * import collection 
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

use App\Repositories\PetaObjekPajak\PetaObjekPajakRepositories;
use App\Repositories\Log\LogRepositories;

class PetaObjekPajakController extends Controller
{
    use Message;

    private $request;
    private $signature;
    private $logRepositories;
    private $petaObjekPajakRepositories;

    public function __construct(
        Request $request,
        PetaObjekPajakRepositories $petaObjekPajakRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->petaObjekPajakRepositories = $petaObjekPajakRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->request = $request;
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * get all record data
     */
    public function data($kdKecamatan, $kdKelurahan = null, $blok = null)
    {
        /**
         * load data from repositories
         */
        $response = $this->petaObjekPajakRepositories->data($kdKecamatan, $kdKelurahan, $blok);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'peta objek pajak');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

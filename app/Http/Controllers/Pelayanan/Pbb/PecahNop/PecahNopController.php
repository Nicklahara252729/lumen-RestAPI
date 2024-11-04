<?php

namespace App\Http\Controllers\Pelayanan\Pbb\PecahNop;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Pelayanan\Pbb\PecahNop\StoreRequest;
use App\Http\Requests\Pelayanan\Pbb\PecahNop\UpdateStatusVerifikasiRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Pelayanan\Pbb\PecahNop\PecahNopRepositories;
use App\Repositories\Log\LogRepositories;

class PecahNopController extends Controller
{
    use Message;

    private $signature;
    private $request;
    private $logRepositories;
    private $pecahNopRepositories;

    public function __construct(
        Request $request,
        PecahNopRepositories $pecahNopRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->pecahNopRepositories = $pecahNopRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * pecah nop
     */
    public function store(StoreRequest $request)
    {
        /**
         * load data from repositories
         */
        $response = $this->pecahNopRepositories->store($request->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'pecah nop value ' . json_encode($request->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update status verifikasi
     */
    public function updateStatusVerifikasi(
        $uuidPelayanan,
        UpdateStatusVerifikasiRequest $request
    ) {
        /**
         * load data from repositories
         */
        $response = $this->pecahNopRepositories->updateStatusVerifikasi(
            $request->all(),
            $uuidPelayanan
        );

        /**
         * save log
         */
        $log = $this->outputLogMessage('update', $uuidPelayanan, json_encode($request->all()), 'status pelayanan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show all data
     */
    public function data($pageSize = null)
    {
        /**
         * load data from repositories
         */
        $response = $this->pecahNopRepositories->data($pageSize);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'pecah nop');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

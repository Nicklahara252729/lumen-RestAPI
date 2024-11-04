<?php

namespace App\Http\Controllers\Setting\Layanan;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Setting\Layanan\UpdateRequest;

/**
 * import
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Setting\Layanan\LayananRepositories;
use App\Repositories\Log\LogRepositories;

class LayananController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $layananRepositories;

    public function __construct(
        Request $request,
        LayananRepositories $layananRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->layananRepositories = $layananRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * update status
     */
    public function updateStatus(
        UpdateRequest $updateRequest,
        $uuidLayanan
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidLayanan, $updateRequest['status'], 'setting layanan');

        /**
         * process begin
         */
        $response = $this->layananRepositories->updateStatus($updateRequest, $uuidLayanan);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

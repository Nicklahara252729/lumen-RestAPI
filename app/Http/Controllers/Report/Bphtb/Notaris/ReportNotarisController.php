<?php

namespace App\Http\Controllers\Report\Bphtb\Notaris;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

 use Illuminate\Http\Request;
 use App\Http\Requests\Report\Notaris\StoreRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Report\Bphtb\Notaris\ReportNotarisRepositories;
use App\Repositories\Log\LogRepositories;

class ReportNotarisController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $reportNotarisRepositories;
    private $request;

    public function __construct(
        Request $request,
        ReportNotarisRepositories $reportNotarisRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->reportNotarisRepositories = $reportNotarisRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * data
     */
    public function data(StoreRequest $storeRequest)
    {
        /**
         * requesting data
         */
        $storeRequest = $storeRequest->all();

        /**
         * process begin
         */
        $response = $this->reportNotarisRepositories->data($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'uuid notaris ' . $storeRequest['uuid_user']);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

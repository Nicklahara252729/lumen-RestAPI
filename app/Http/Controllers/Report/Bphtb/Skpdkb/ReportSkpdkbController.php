<?php

namespace App\Http\Controllers\Report\Bphtb\Skpdkb;

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

use App\Repositories\Report\Bphtb\Skpdkb\ReportSkpdkbRepositories;
use App\Repositories\Log\LogRepositories;

class ReportSkpdkbController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $reportSkpdkbRepositories;
    private $request;

    public function __construct(
        Request $request,
        ReportSkpdkbRepositories $reportSkpdkbRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->reportSkpdkbRepositories = $reportSkpdkbRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * data
     */
    public function data($statusBayar)
    {
        /**
         * process begin
         */
        $startDate = $this->request->get('startDate');
        $endDate = $this->request->get('endDate');
        $response = $this->reportSkpdkbRepositories->data($statusBayar, $startDate, $endDate);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'status bayar ' . $statusBayar);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

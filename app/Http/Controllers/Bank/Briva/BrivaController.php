<?php

namespace App\Http\Controllers\Bank\Briva;

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

use App\Repositories\Bank\Briva\BrivaRepositories;
use App\Repositories\Log\LogRepositories;

class BrivaController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $brivaRepositories;

    public function __construct(
        Request $request,
        BrivaRepositories $brivaRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->brivaRepositories = $brivaRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }


    /**
     * create briva
     */
    public function create()
    {
        /**
         * load data from repositories
         */
        $response = $this->brivaRepositories->create();

        /**
         * save log
         */
        $log = $this->outputLogMessage('generate', 'briva', json_encode($response));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

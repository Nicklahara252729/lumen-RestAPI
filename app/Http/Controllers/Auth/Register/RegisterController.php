<?php

/**
 * file collection
 */

namespace App\Http\Controllers\Auth\Register;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import form rqeuest
 */

use App\Http\Requests\Auth\Register\StoreNotarisRequest;

/**
 * import repositories
 */

use App\Repositories\Auth\Register\RegisterRepositories;
use App\Repositories\Log\LogRepositories;

class RegisterController extends Controller
{
    use Message;

    private $registerRepositories;
    private $logRepositories;

    public function __construct(
        LogRepositories $logRepositories,
        RegisterRepositories $registerRepositories
    ) {

        /**
         * init repositories
         */
        $this->registerRepositories = $registerRepositories;
        $this->logRepositories = $logRepositories;
    }

    /**
     * store register notaris
     */
    public function storeNotaris(StoreNotarisRequest $storeNotarisRequest)
    {
        /**
         * requesting data
         */
        $storeNotarisRequest = $storeNotarisRequest->all();

        /**
         * process
         */
        $response = $this->registerRepositories->storeNotaris($storeNotarisRequest);

        /**
         * response
         */
        return response()->json($response);
    }
}

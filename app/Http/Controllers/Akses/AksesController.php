<?php

namespace App\Http\Controllers\Akses;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Akses\StoreRequest;
use App\Http\Requests\Akses\UpdateRequest;

/**
 * import repositories 
 */

use App\Repositories\Akses\AksesRepositories;
use App\Repositories\Log\LogRepositories;

class AksesController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $request;
    private $aksesRepositories;

    public function __construct(
        Request $request,
        AksesRepositories $aksesRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->aksesRepositories = $aksesRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * get all record data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->aksesRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'akses');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid akses
     */
    public function get($uuidAkses)
    {
        /**
         * process to database
         */
        $response = $this->aksesRepositories->get($uuidAkses);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'akses', 'uuid akses :' . $uuidAkses);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by role dan sub bidang
     */
    public function getByRoleBidang($role, $uuidBidang)
    {
        /**
         * process to database
         */
        $response = $this->aksesRepositories->getByRoleBidang($role, $uuidBidang);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'akses', 'uuid bidang :' . $uuidBidang . ' role ' . $role);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store data
     */
    public function store(
        StoreRequest $storeRequest
    ) {
        /**
         * requesting data
         */
        $storeRequest = $storeRequest->all();

        /**
         * process begin
         */
        $response = $this->aksesRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'bidang ' . json_encode($storeRequest['role']));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

<?php

namespace App\Http\Controllers\Pelayanan\Pbb\PerubahanStatusNop;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Pelayanan\Pbb\PerubahanStatusNop\UpdateRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Pelayanan\Pbb\PerubahanStatusNop\PerubahanStatusNopRepositories;
use App\Repositories\Log\LogRepositories;

class PerubahanStatusNopController extends Controller
{
    use Message;

    private $signature;
    private $request;
    private $logRepositories;
    private $perubahanStatusNopRepositories;

    public function __construct(
        Request $request,
        PerubahanStatusNopRepositories $perubahanStatusNopRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->perubahanStatusNopRepositories = $perubahanStatusNopRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * update status nop
     */
    public function update(UpdateRequest $request)
    {
        /**
         * load data from repositories
         */
        $request = $request->all();
        $response = $this->perubahanStatusNopRepositories->update($request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('update', $request['nop'], json_encode($request), 'status nop');
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
        $response = $this->perubahanStatusNopRepositories->data($pageSize);

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

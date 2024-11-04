<?php

namespace App\Http\Controllers\Bank\Bpn;

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

use App\Repositories\Bank\Bpn\BpnRepositories;
use App\Repositories\Log\LogRepositories;

class BpnController extends Controller
{
    use Message;

    private $bpnRepositories;
    private $request;
    private $signature;
    private $logRepositories;

    public function __construct(
        Request $request,
        bpnRepositories $bpnRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->bpnRepositories = $bpnRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize static value
         */
        $this->request = $request;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * update bptbh for kabbid
     */
    public function bphtbService($uuidPelayananBphtb)
    {
        /**
         * load data from repositories
         */
        $response = $this->bpnRepositories->bphtbService($uuidPelayananBphtb);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * data getBPHTBService
     */
    public function getBPHTBService()
    {
        /**
         * load data from repositories
         */
        $response = $this->bpnRepositories->getBPHTBService($this->request);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * data getPBBService
     */
    public function getPBBService()
    {
        /**
         * load data from repositories
         */
        $response = $this->bpnRepositories->getPBBService();

        /**
         * response
         */
        return response()->json($response);
    }
}

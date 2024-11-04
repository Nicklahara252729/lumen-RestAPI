<?php

namespace App\Http\Controllers\Dashboard;

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

use App\Repositories\Dashboard\DashboardRepositories;
use App\Repositories\Log\LogRepositories;

class DashboardController extends Controller
{
    use Message;

    private $signature;
    private $bidangRepositories;
    private $logRepositories;
    private $dashboardRepositories;

    public function __construct(
        Request $request,
        DashboardRepositories $dashboardRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->dashboardRepositories = $dashboardRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * total data permohonan
     */
    public function totalPermohonan()
    {
        /**
         * load data from repositories
         */
        $response = $this->dashboardRepositories->totalPermohonan();

        /**
         * save log
         */
        $log = $this->outputLogMessage('total', 'permohonan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * total per layanan
     */
    public function totalPerLayanan()
    {
        /**
         * load data from repositories
         */
        $response = $this->dashboardRepositories->totalPerLayanan();

        /**
         * save log
         */
        $log = $this->outputLogMessage('total', 'per layanan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show data by kecamatan or kelurahan 
     */
    public function dataByKecamatanOrKelurahan($kdKecamatan, $kdKelurahan = null)
    {
        /**
         * load data from repositories
         */
        $response = $this->dashboardRepositories->dataByKecamatanOrKelurahan($kdKecamatan, $kdKelurahan);

        /**
         * save log
         */
        $message = is_null($kdKelurahan) ? 'by kode kecamatan' : 'by kode kecamatan dan kelurahan';
        $log = $this->outputLogMessage('total', $message);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * total data permohonan bphtb
     */
    public function totalPermohonanBphtb()
    {
        /**
         * load data from repositories
         */
        $response = $this->dashboardRepositories->totalPermohonanBphtb();

        /**
         * save log
         */
        $log = $this->outputLogMessage('total', 'permohonan bphtb');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

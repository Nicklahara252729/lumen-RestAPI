<?php

namespace App\Http\Controllers\Report\Pbb;

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

use App\Repositories\Report\Pbb\ReportPbbRepositories;
use App\Repositories\Log\LogRepositories;

class ReportPbbController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $reportPbbRepositories;
    private $request;

    public function __construct(
        Request $request,
        ReportPbbRepositories $reportPbbRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->reportPbbRepositories = $reportPbbRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * rekap ketetapan
     */
    public function rekapKetetapan($tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->reportPbbRepositories->rekapKetetapan($tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'rekap ketetapan tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * detail ketetapan
     */
    public function detailKetetapan($tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->reportPbbRepositories->detailKetetapan($tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'report detail ketetapan', json_encode($this->request->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * rincian ketetapan
     */
    public function rincianKetetapan()
    {
        /**
         * load data from repositories
         */
        $response = $this->reportPbbRepositories->rincianKetetapan($this->request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'report rincian dhkp');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * rincian piutang
     */
    public function rincianPiutang()
    {
        /**
         * load data from repositories
         */
        $response = $this->reportPbbRepositories->rincianPiutang($this->request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'report rincian piutang', json_encode($this->request->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * rekap piutang
     */
    public function rekapPiutang()
    {
        /**
         * load data from repositories
         */
        $response = $this->reportPbbRepositories->rekapPiutang($this->request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'report rekap piutang', json_encode($this->request->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * rincian realisasi
     */
    public function rincianRealisasi()
    {
        /**
         * load data from repositories
         */
        $response = $this->reportPbbRepositories->rincianRealisasi($this->request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'report rincian realisasi', json_encode($this->request->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * rekap realisasi
     */
    public function rekapRealisasi($startDate, $endDate)
    {
        /**
         * load data from repositories
         */
        $response = $this->reportPbbRepositories->rekapRealisasi($startDate, $endDate);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'report rekap realisasi');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * detail realisasi
     */
    public function detailRealisasi($startDate, $endDate)
    {
        /**
         * load data from repositories
         */
        $response = $this->reportPbbRepositories->detailRealisasi($startDate, $endDate);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'report detail realisasi');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

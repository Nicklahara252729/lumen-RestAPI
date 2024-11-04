<?php

namespace App\Http\Controllers\OperatorLapangan;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import request
 */

use Illuminate\Http\Request;
use App\Http\Requests\OperatorLapangan\StoreRequest;
use App\Http\Requests\OperatorLapangan\StoreNopdRequest;
use App\Http\Requests\OperatorLapangan\StoreRegpribadiRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\OperatorLapangan\OperatorLapanganRepositories;
use App\Repositories\Log\LogRepositories;

class OperatorLapanganController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $operatorLapanganRepositories;
    private $request;

    public function __construct(
        Request $request,
        OperatorLapanganRepositories $operatorLapanganRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->operatorLapanganRepositories = $operatorLapanganRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->request = $request;
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * get all record
     */
    public function data()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * autocomplete
     */
    public function autocomplete()
    {

        /**
         * load data from repositories
         */
        $key = $this->request->get('key');
        $response = $this->operatorLapanganRepositories->autocomplete($key);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'objek pajak');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store objek pajak
     */
    public function store(StoreRequest $request)
    {
        /**
         * process to database
         */
        $request = $request->all();
        $response = $this->operatorLapanganRepositories->store($request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'operator lapangan value ' . json_encode($request));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record pajak hiburan
     */
    public function dataHiburan()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataHiburan();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan pajak hiburan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record pajak hotel
     */
    public function dataHotel()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataHotel();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan pajak hotel');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record pajak parkir
     */
    public function dataParkir()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataParkir();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan pajak parkir');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record pajak pat
     */
    public function dataPat()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataPat();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan pajak pat');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record pajak penerangan
     */
    public function dataPenerangan()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataPenerangan();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan pajak penerangan');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record pajak pln
     */
    public function dataPln()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataPln();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan pajak pln');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record pajak reklame
     */
    public function dataReklame()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataReklame();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan pajak reklame');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record pajak walet
     */
    public function dataWalet()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataWalet();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan pajak walet');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store regpribadi
     */
    public function storeRegpribadi(StoreRegpribadiRequest $request)
    {
        /**
         * process to database
         */
        $request = $request->all();
        $response = $this->operatorLapanganRepositories->storeRegpribadi($request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'reg pribadi value ' . json_encode($request));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store nopd
     */
    public function storeNopd(StoreNopdRequest $request)
    {
        /**
         * process to database
         */
        $request = $request->all();
        $response = $this->operatorLapanganRepositories->storeNopd($request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'nopd value ' . json_encode($request));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record reg pribadi
     */
    public function dataRegpribadi()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataRegpribadi();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan data regpribadi');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get all record nopd
     */
    public function dataNopd()
    {

        /**
         * load data from repositories
         */
        $response = $this->operatorLapanganRepositories->dataNopd();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan data NOPD');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * search
     */
    public function search()
    {

        /**
         * load data from repositories
         */
        $key = $this->request->get('key');
        $idKecamatan = $this->request->get('idKecamatan');
        $idKelurahan = $this->request->get('idKelurahan');
        $response = $this->operatorLapanganRepositories->search($key, $idKecamatan, $idKelurahan);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'operator lapangan pencarian');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

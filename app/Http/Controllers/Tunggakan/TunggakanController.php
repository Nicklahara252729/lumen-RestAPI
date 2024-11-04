<?php

namespace App\Http\Controllers\Tunggakan;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Tunggakan\UpdateRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Tunggakan\TunggakanRepositories;
use App\Repositories\Log\LogRepositories;

class TunggakanController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $tunggakanRepositories;

    public function __construct(
        Request $request,
        TunggakanRepositories $tunggakanRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->tunggakanRepositories = $tunggakanRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * all record data
     */
    public function data($kdKecamatan = null, $kdKelurahan = null)
    {
        $role = authAttribute()['role'];
        $kdKecamatan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKecamatan : authAttribute()['kd_kecamatan'];
        $kdKelurahan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKelurahan : authAttribute()['kd_kelurahan'];

        /**
         * load data from repositories
         */
        $response = $this->tunggakanRepositories->data($kdKecamatan, $kdKelurahan);

        /**
         * save log
         */
        $moreValue = 'kode kecamatan : ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan;
        $log = $this->outputLogMessage('search', 'tunggakan / piutang', $moreValue);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by kd blok
     */
    public function dataNopByKdBlok($kdBlok, $kdKecamatan = null, $kdKelurahan = null)
    {
        $role = authAttribute()['role'];
        $kdKecamatan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKecamatan : authAttribute()['kd_kecamatan'];
        $kdKelurahan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKelurahan : authAttribute()['kd_kelurahan'];

        /**
         * process to database
         */
        $response = $this->tunggakanRepositories->dataNopByKdBlok($kdBlok, $kdKecamatan, $kdKelurahan);

        /**
         * save log
         */
        $moreValue = 'kode kecamatan : ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' kode blok ' . $kdBlok;
        $log = $this->outputLogMessage('search', 'tunggakan / piutang', $moreValue);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update data
     */
    public function update(
        UpdateRequest $updateRequest,
        $param,
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $param, json_encode($updateRequest), 'tunggakan');

        /**
         * process begin
         */
        $response = $this->tunggakanRepositories->update($updateRequest, $param);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}

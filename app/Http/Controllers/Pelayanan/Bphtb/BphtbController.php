<?php

namespace App\Http\Controllers\Pelayanan\Bphtb;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import custom request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Pelayanan\Bphtb\StoreRequest;
use App\Http\Requests\Pelayanan\Bphtb\UpdateRequest;
use App\Http\Requests\Pelayanan\Bphtb\UpdateStatusVerifikasiRequest;
use App\Http\Requests\Pelayanan\Bphtb\StoreStatusDitolak;
use App\Http\Requests\Pelayanan\Bphtb\UpdatePerhitunganNjopRequest;
use App\Http\Requests\Pelayanan\Bphtb\UpdatePerhitunganBphtbRequest;
use App\Http\Requests\Pelayanan\Bphtb\StorePembayaranManualRequest;
use App\Http\Requests\Pelayanan\Bphtb\UpdateFullRequest;


/**
 * import helper
 */

use App\Libraries\CheckerHelpers;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Generator;

/**
 * import repositories
 */

use App\Repositories\Pelayanan\Bphtb\BphtbRepositories;
use App\Repositories\Log\LogRepositories;

class BphtbController extends Controller
{
    use Message, Generator;

    private $signature;
    private $request;
    private $logRepositories;
    private $bphtbRepositories;
    private $checkerHelper;

    public function __construct(
        Request $request,
        BphtbRepositories $bphtbRepositories,
        LogRepositories $logRepositories,
        CheckerHelpers $checkerHelper
    ) {
        /**
         * defined repositories
         */
        $this->bphtbRepositories = $bphtbRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * defined helper
         */
        $this->checkerHelper = $checkerHelper;

        /**
         * defined component
         */
        $this->request = $request;
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * save data
     */
    public function store(StoreRequest $storeRequest)
    {
        /**
         * load data from repositories
         */
        $request = $storeRequest->all();
        $request['no_registrasi'] = $this->nomorRegistrasiBphtb();
        $response = $this->bphtbRepositories->store($request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'bphtb value ' . json_encode($request));
        $logBphtb = $this->outputLogMessageBphtb('store', json_encode($request));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        $this->logRepositories->saveLogBphtb($logBphtb['action'], $logBphtb['message'], $this->signature, $request['no_registrasi']);

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
        $statusVerifikasi = $this->request->get('status');
        $deleted = $this->request->get('deleted');
        $response = $this->bphtbRepositories->data($statusVerifikasi, $pageSize, $deleted);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'BPHTB');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * autocomplete data by nop
     */
    public function autocomplete($nop, $tahun = null)
    {
        /**
         * process to database
         */
        $response = $this->bphtbRepositories->autocomplete($nop, $tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'autocomplete BPHTB', 'nop :' . $nop);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by nop
     */
    public function get($param)
    {
        /**
         * process to database
         */
        $response = $this->bphtbRepositories->get($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'pelayanan BPHTB', 'parameter :' . $param);
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
        $uuidPelayananBphtb,
        UpdateRequest $updateRequest
    ) {
        /**
         * get no registrasi
         */
        $checkData = $this->checkerHelper->pelayananBphtbChecker(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb]);
        $noRegistrasi = $checkData->no_registrasi;

        /**
         * set log 
         */
        $request = $updateRequest->all();
        $log = $this->outputLogMessage('update', $uuidPelayananBphtb, json_encode($request), 'pelayanan bphtb');
        $logBphtb = $this->outputLogMessageBphtb('update', $checkData, json_encode($request));

        /**
         * load data from repositories
         */
        $response = $this->bphtbRepositories->update($request, $uuidPelayananBphtb);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        $this->logRepositories->saveLogBphtb($logBphtb['action'], $logBphtb['message'], $this->signature, $noRegistrasi);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update status verifikasi
     */
    public function updateStatusVerifikasi(
        $uuidPelayananBphtb,
        UpdateStatusVerifikasiRequest $request
    ) {
        /**
         * get no registrasi
         */
        $checkData = $this->checkerHelper->pelayananBphtbChecker(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb]);
        $noRegistrasi = $checkData->no_registrasi;

        /**
         * load data from repositories
         */
        $request = $request->all();
        $moreValue = $request['status_verifikasi'] . ' dengan data lainnya ' . collect($request)->except(['status_verifikasi']);
        $response = $this->bphtbRepositories->updateStatusVerifikasi($request, $uuidPelayananBphtb);

        /**
         * save log
         */
        $log = $this->outputLogMessage('update', $uuidPelayananBphtb, json_encode($request), 'status pelayanan');
        $logBphtb = $this->outputLogMessageBphtb('update status verifikasi', $noRegistrasi, $moreValue);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        $this->logRepositories->saveLogBphtb($logBphtb['action'], $logBphtb['message'], $this->signature, $noRegistrasi);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show all data riwayat ditolak
     */
    public function riwayatDitolak($noRegistrasi)
    {
        /**
         * load data from repositories
         */
        $response = $this->bphtbRepositories->riwayatDitolak($noRegistrasi);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'BPHTB');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * detail
     */
    public function detail($uuidPelayananBphtb)
    {
        /**
         * load data from repositories
         */
        $response = $this->bphtbRepositories->detail($uuidPelayananBphtb);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'BPHTB');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store status pelayanan ditolak
     */
    public function storeStatusDitolak(StoreStatusDitolak $storeStatusDitolak)
    {
        /**
         * requesting data
         */
        $storeStatusDitolak = $storeStatusDitolak->all();

        /**
         * process begin
         */
        $response = $this->bphtbRepositories->storeStatusDitolak($storeStatusDitolak);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'nomor registrasi ' . $storeStatusDitolak['no_registrasi']);
        $logBphtb = $this->outputLogMessageBphtb('reject');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        $this->logRepositories->saveLogBphtb($logBphtb['action'], $logBphtb['message'], $this->signature, $storeStatusDitolak['no_registrasi']);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * serach by no rgistrasi
     */
    public function search($pageSize = null)
    {
        /**
         * load data from repositories
         */
        $statusVerifikasi = $this->request->get('status');
        if (!empty($this->request->get('noreg'))) :
            $condition = ['column' => 'no_registrasi', 'value' => $this->request->get('noreg')];
        elseif (!empty($this->request->get('nama'))) :
            $condition = ['column' => 'nama_wp_2', 'value' => $this->request->get('nama')];
        elseif (!@empty($this->request->get('nop'))) :
            $condition = ['column' => 'nop', 'value' => $this->request->get('nop')];
        elseif (!empty($this->request->get('notaris'))) :
            $condition = ['column' => 'created_by', 'value' => $this->request->get('notaris')];
        elseif (!empty($this->request->get('nik'))) :
            $condition = ['column' => 'nik', 'value' => $this->request->get('nik')];
        endif;
        $condition = array_merge($condition, ['status_verifikasi' => $statusVerifikasi]);
        $deleted = $this->request->get('deleted');
        $response = $this->bphtbRepositories->search($condition, $pageSize, $deleted);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'BPHTB');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update perhitungan njop
     */
    public function updatePerhitunganNjop(
        $uuidPelayananBphtb,
        UpdatePerhitunganNjopRequest $updateRequest
    ) {
        /**
         * get no registrasi
         */
        $checkData = $this->checkerHelper->pelayananBphtbChecker(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb]);
        $noRegistrasi = $checkData->no_registrasi;

        /**
         * load data from repositories
         */
        $updateRequest = $updateRequest->all();
        $response = $this->bphtbRepositories->updatePerhitunganNjop($updateRequest, $uuidPelayananBphtb);

        /**
         * save log
         */
        $log = $this->outputLogMessage('update', $uuidPelayananBphtb, json_encode($updateRequest), 'pelayanan bphtb');
        $logBphtb = $this->outputLogMessageBphtb('perhitungan njop', json_encode($updateRequest));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        $this->logRepositories->saveLogBphtb($logBphtb['action'], $logBphtb['message'], $this->signature, $noRegistrasi);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * delete dokumen
     */
    public function deleteDokumen($uuidPelayananBphtb, $dokumen)
    {
        /**
         * get no registrasi
         */
        $checkData = $this->checkerHelper->pelayananBphtbChecker(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb]);
        $noRegistrasi = $checkData->no_registrasi;

        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', 'dokumen', null, 'pelayanan Bphtb');
        $logBphtb = $this->outputLogMessageBphtb('delete document', str_replace('_', ' ', $dokumen));

        /**
         * process begin
         */
        $response = $this->bphtbRepositories->deleteDokumen($uuidPelayananBphtb, $dokumen);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        $this->logRepositories->saveLogBphtb($logBphtb['action'], $logBphtb['message'], $this->signature, $noRegistrasi);

        /** 
         * response
         */
        return response()->json($response);
    }

    /**
     * update perhitungan bphtb
     */
    public function updatePerhitunganBphtb(
        $uuidPelayananBphtb,
        UpdatePerhitunganBphtbRequest $updateRequest
    ) {
        /**
         * get no registrasi
         */
        $checkData = $this->checkerHelper->pelayananBphtbChecker(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb]);
        $noRegistrasi = $checkData->no_registrasi;

        /**
         * load data from repositories
         */
        $updateRequest = $updateRequest->all();
        $response = $this->bphtbRepositories->updatePerhitunganBphtb($updateRequest, $uuidPelayananBphtb);

        /**
         * save log
         */
        $log = $this->outputLogMessage('update', $uuidPelayananBphtb, json_encode($updateRequest), 'pelayanan bphtb');
        $logBphtb = $this->outputLogMessageBphtb('perhitungan bphtb', json_encode($updateRequest));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        $this->logRepositories->saveLogBphtb($logBphtb['action'], $logBphtb['message'], $this->signature, $noRegistrasi);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * delete data
     */
    public function delete($uuidPelayananBphtb)
    {
        /**
         * get no registrasi
         */
        $checkData = $this->checkerHelper->pelayananBphtbChecker(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb]);
        $noRegistrasi = $checkData->no_registrasi;

        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', 'data', null, 'pelayanan Bphtb');
        $logBphtb = $this->outputLogMessageBphtb('delete');

        /**
         * process begin
         */
        $response = $this->bphtbRepositories->delete($uuidPelayananBphtb);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        $this->logRepositories->saveLogBphtb($logBphtb['action'], $logBphtb['message'], $this->signature, $noRegistrasi);

        /** 
         * response
         */
        return response()->json($response);
    }

    /**
     * save data
     */
    public function storePembayaranManual(StorePembayaranManualRequest $storeRequest)
    {
        /**
         * load data from repositories
         */
        $request = $storeRequest->all();
        $response = $this->bphtbRepositories->storePembayaranManual($request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'sts value ' . json_encode($request));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update full data
     */
    public function updateFull(
        $uuidPelayananBphtb,
        UpdateFullRequest $updateRequest
    ) {
        /**
         * get no registrasi
         */
        $checkData = $this->checkerHelper->pelayananBphtbChecker(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb]);
        $noRegistrasi = $checkData->no_registrasi;

        /**
         * set log 
         */
        $request = $updateRequest->all();
        $log = $this->outputLogMessage('update', $uuidPelayananBphtb, json_encode($request), 'pelayanan bphtb');
        $logBphtb = $this->outputLogMessageBphtb('update', $checkData, json_encode($request));

        /**
         * load data from repositories
         */
        $response = $this->bphtbRepositories->updateFull($request, $uuidPelayananBphtb);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);
        $this->logRepositories->saveLogBphtb($logBphtb['action'], $logBphtb['message'], $this->signature, $noRegistrasi);

        /**
         * response
         */
        return response()->json($response);
    }
}

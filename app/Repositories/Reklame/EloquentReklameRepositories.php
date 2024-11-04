<?php

namespace App\Repositories\Reklame;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Generator;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Reklame\ReklameRepositories;

class EloquentReklameRepositories implements ReklameRepositories
{
    use Message, Response, Generator;

    private $checkerHelpers;
    private $paginateHelpers;
    private $secondDb;
    private $thirdDb;
    private $reklame;
    private $stsHistory;
    private $year;
    private $datetime;
    private $kodeReklame;

    public function __construct(
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers
    ) {

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->secondDb = DB::connection('second_mysql');
        $this->thirdDb = DB::connection('third_mysql');
        $this->reklame = $this->thirdDb->table('x_reklame');
        $this->stsHistory = $this->secondDb->table('STS_History');
        $this->year = Carbon::now()->format('Y');
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->kodeReklame = globalAttribute()['stsReklame'];
    }

    /**
     * data
     */
    public function data($status)
    {
        try {

            $data = $this->reklame->where('status', $status);
            if ($status == 1) :
                $data = $data->where(function ($query) {
                    $query->whereRaw("user_verifi NOT REGEXP '^[0-9]+$'")
                        ->whereNotNull('user_verifi')
                        ->where('user_verifi', '<>', '');
                });
            endif;
            $data = $data->orderByDesc('reklame_id')->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($data)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * update status verifikasi
     */
    public function  verifikasi($request, $reklameId)
    {
        DB::beginTransaction();
        try {

            /**
             * check if data reklame was exists
             */
            $getDataReklame = $this->reklame->where('reklame_id', $reklameId)->first();
            if (is_null($getDataReklame)) :
                throw new \Exception($this->outputMessage('not found', 'reklame'));
            endif;

            /**
             * update data reklame
             */
            $updateReklame = $this->reklame->where('reklame_id', $reklameId)->update($request);
            if (!$updateReklame) :
                throw new \Exception($this->outputMessage('update fail', 'reklame'));
            endif;

            /**
             * jika status verifiksai
             * maka akan membuat sts history
             */
            if ($request['status'] == 1) :

                /**
                 * set value
                 */
                $stsValue = [
                    'Tahun' => $this->year,
                    'No_STS' => '1259' . $this->kodeReklame . $this->noStsReklame(), // 1259 - 410109 - 410112 (air tanah) - no urut
                    'Tgl_STS' => $this->datetime,
                    'No_NOP' => $getDataReklame->sptpd, // no sptpd dari table reklame
                    'No_Pokok_WP' => $getDataReklame->nopd, // nopd dari table reklame
                    'Nama_Pemilik' => $getDataReklame->nama_pemilik,
                    'Alamat_Pemilik' => $getDataReklame->alamat,
                    'Jn_Pajak' => '410109',
                    'Nm_Pajak' => 'Pajak Reklame',
                    'Nilai' => $getDataReklame->pajak_terhutang,
                    'Status_Bayar' => 0,
                ];

                /**
                 * save to sts
                 */
                $saveSts = $this->stsHistory->insert($stsValue);
                if (!$saveSts) :
                    throw new \Exception($this->outputMessage('unsaved', 'STS History'));
                endif;
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', 'status verifikasi'));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * sts tertinggi
     */
    public function stsTertinggi()
    {
        try {

            $data = $this->stsHistory->whereRaw('MID(No_STS,1,10) = "1259' . $this->kodeReklame . '"')
                ->where(['Tahun' => $this->year, 'Status_Bayar' => 1])
                ->orderByDesc('Nilai')
                ->limit(10)
                ->get();

            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data sts
     */
    public function dataSts()
    {
        try {

            $data = $this->stsHistory->whereRaw('MID(No_STS,1,10) = "1259' . $this->kodeReklame . '"')
                ->where(['Tahun' => $this->year, 'Status_Bayar' => 1])
                ->orderByDesc('kode')
                ->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($data)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

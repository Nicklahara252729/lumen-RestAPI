<?php

namespace App\Repositories\Kaban;

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

use App\Repositories\Kaban\KabanRepositories;

class EloquentKabanRepositories implements KabanRepositories
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
    private $kodePat;

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
        $this->kodePat = globalAttribute()['stsPat'];
    }

    /**
     * all sts
     */
    public function sts()
    {
        try {

            $data = $this->stsHistory->whereRaw('MID(No_STS,1,10) IN ("1259' . $this->kodeReklame . '","1259' . $this->kodePat . '")')
                ->where(['Tahun' => $this->year, 'Status_Bayar' => 1])
                ->orderByDesc('Nilai')
                ->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($data)), $dataPaginate);
        } catch (\Exception $e) {
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

            $data = $this->stsHistory->whereRaw('MID(No_STS,1,10) IN ("1259' . $this->kodeReklame . '","1259' . $this->kodePat . '")')
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
     * get single data sts by no sts
     */
    public function detailSts($noSts)
    {
        try {

            $getData = $this->stsHistory->where('No_STS', $noSts)->first();
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'STS History'));
            endif;

            $response = $this->successData($this->outputMessage('data', 1), $getData);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

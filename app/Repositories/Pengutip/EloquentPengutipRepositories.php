<?php

namespace App\Repositories\Pengutip;

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

use App\Repositories\Pengutip\PengutipRepositories;

class EloquentPengutipRepositories implements PengutipRepositories
{
    use Message, Response, Generator;

    private $checkerHelpers;
    private $paginateHelpers;
    private $secondDb;
    private $thirdDb;
    private $pengutipRestoran;
    private $restoran;
    private $stsHistory;
    private $year;
    private $date;
    private $kodeRestoran;
    private $nopd;

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
        $this->pengutipRestoran = $this->thirdDb->table('x_pengutip_restoran');
        $this->restoran = $this->thirdDb->table('x_restoran');
        $this->stsHistory = $this->secondDb->table('STS_History');
        $this->year = Carbon::now()->format('Y');
        $this->date = Carbon::now()->toDateString();
        $this->kodeRestoran = globalAttribute()['stsRestoran'];
        $this->nopd = $this->thirdDb->table('x_nopd');
    }

    /**
     * data
     */
    public function data()
    {
        try {

            $uuidUser = authAttribute()['id'];
            $data = $this->pengutipRestoran->select(
                'id',
                'sptpd',
                'nama_op',
                'alamat',
                'tanggal_bayar',
                'masa_pajak',
                'jumlah',
                'created_at'
            )
                ->where('created_by', $uuidUser)
                ->orderByDesc('id')
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
     * data restoran
     */
    public function dataRestoran()
    {
        try {

            $data = $this->restoran->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($data)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * autocomplete
     */
    public function autocomplete($nopd)
    {
        try {

            $getData = $this->nopd->select(
                'nama_usaha AS nama_objek',
                'alamat',
            )
                ->where('nopd', $nopd)
                ->first();
            $data = [
                'nama_objek' => $getData->nama_objek,
                'alamat'     => $getData->alamat,
                'tgl_bayar'  => $this->date,
                'masa_bayar' => $this->date,
                'jumlah'     => globalAttribute()['jumlahPajakRestoran']
            ];

            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * store data pengutip
     */
    public function  store($request)
    {
        DB::beginTransaction();
        try {
            $request['sptpd'] = globalAttribute()['stsRestoran'] . $this->noStsRestoran();
            $saveData = $this->pengutipRestoran->insert($request);
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', 'pajak restoran'));
            endif;
            DB::commit();
            $response  = $this->success($this->outputMessage('saved', 'pajak restoran'));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

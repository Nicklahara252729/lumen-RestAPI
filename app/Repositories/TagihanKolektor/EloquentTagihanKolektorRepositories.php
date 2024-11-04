<?php

namespace App\Repositories\TagihanKolektor;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Generator;
use App\Traits\Calculation;

/**
 * import models
 */

use App\Models\TagihanKolektor\TagihanKolektor;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import repositories
 */

use App\Repositories\TagihanKolektor\TagihanKolektorRepositories;

class EloquentTagihanKolektorRepositories implements TagihanKolektorRepositories
{
    use Message, Response, Generator, Calculation;

    private $tagihanKolektor;
    private $checkerHelpers;
    private $paginateHelpers;
    private $datetime;
    private $tagihanKolektorAsTk;

    public function __construct(
        TagihanKolektor $tagihanKolektor,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers
    ) {
        /**
         * initialize model
         */
        $this->tagihanKolektor = $tagihanKolektor;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->tagihanKolektorAsTk = DB::table('tagihan_kolektor as tk');
    }

    /**
     * get all record
     */
    public function data($pageSize)
    {
        try {
            /**
             * data tagihan kolektor
             */
            $data = $this->tagihanKolektorAsTk->select(
                'tk.nop',
                'tk.tahun_pajak',
                'tk.total_tagihan',
                'tk.nomor_tagihan',
                'tk.kode_bayar',
                'tk.denda',
                'tk.pokok',
                DB::raw("DATE_FORMAT(tk.created_at, '%d %M %Y') AS created_at"),
                'rk.NM_KECAMATAN AS nama_kecamatan',
                'rkl.NM_KELURAHAN AS nama_kelurahan',
                DB::raw("COALESCE(dop.JALAN_OP, '-') AS alamat_wp"),
                'u.name AS nama',
                'tk.nama_wp'
            )
                ->leftJoin('ref_kecamatan as rk', 'rk.KD_KECAMATAN', '=', DB::raw("MID(tk.nop, 5, 3)"))
                ->leftJoin('ref_kelurahan as rkl', function ($join) {
                    $join->on('rkl.KD_KECAMATAN', '=', DB::raw("MID(tk.nop, 5, 3)"))
                        ->on('rkl.KD_KELURAHAN', '=', DB::raw("MID(tk.nop, 8, 3)"));
                })
                ->leftJoin('dat_objek_pajak as dop', function ($join) {
                    $join->on(DB::raw("CONCAT(dop.KD_PROPINSI, dop.KD_DATI2, dop.KD_KECAMATAN, dop.KD_KELURAHAN, dop.KD_BLOK, dop.NO_URUT, dop.KD_JNS_OP)"), '=', 'tk.nop');
                })
                ->leftJoin('users as u', 'u.uuid_user', '=', 'tk.uuid_user');
            if (authAttribute()['role'] == 'petugas lapangan' || authAttribute()['role'] == 'kolektor') :
                $data = $data->where('tk.uuid_user', authAttribute()['id']);
            endif;
            $data = $data->orderByDesc('tk.id')->get();

            /**
             * set page size
             */
            if ($pageSize == 'all') :
                $dataPaginate = $data;
            else :
                $collectionObject = collect($data);
                $pageSize = is_null($pageSize) ? 10 : $pageSize;
                $dataPaginate = $this->paginateHelpers->paginate($collectionObject, $pageSize);
            endif;

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get single data by nop
     */
    public function get($nop)
    {
        try {

            /**
             * where condition
             */
            $role = authAttribute()['role'];
            $where = ['nop' => $nop];
            $where = $role == 'petugas lapangan' && $role == 'kolektor' ? array_merge(['tagihan_kolektor.uuid_user' => authAttribute()['id']]) : $where;

            /**
             * data tagihan kolektor
             */
            $data = $this->tagihanKolektor->select(
                'nop',
                'tahun_pajak',
                'total_tagihan',
                'nomor_tagihan',
                'kode_bayar',
                'denda',
                'pokok',
                DB::raw("DATE_FORMAT(tagihan_kolektor.created_at, '%d %M %Y') AS created_at"),
                'name'
            )
                ->join('users', 'tagihan_kolektor.uuid_user', '=', 'users.uuid_user')
                ->where($where)
                ->first();

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', $data), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }


    /**
     * store tagihan kolektor data to db
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $nomorTagihan = $request['nomor_tagihan'];
            $kodeBayar = $request['kode_bayar'];
            $tagihanKolektor = [];
            foreach ($request['nop'] as $key => $item) :
                $setData = [
                    'uuid_tagihan_kolektor' => (string) Str::orderedUuid(),
                    'nop' => $item,
                    'tahun_pajak' => $request['tahun_pajak'][$key],
                    'total_tagihan' => $request['total_tagihan'][$key],
                    'uuid_user' => $request['uuid_user'],
                    'denda' => $request['denda'][$key],
                    'pokok' => $request['pokok'][$key],
                    'nama_wp' => $request['nama_wp'][$key],
                    'nomor_tagihan' => $nomorTagihan,
                    'kode_bayar' => $kodeBayar,
                    'created_at' => $this->datetime,
                    'updated_at' => $this->datetime
                ];
                array_push($tagihanKolektor, $setData);
            endforeach;

            /**
             * save data tagihan kolektor
             */
            $saveData = $this->tagihanKolektor->insert($tagihanKolektor);
            $listNop = implode(",", json_decode(json_encode($request['nop'])));
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', 'tagihan NOP ' . $listNop));
            endif;
            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'tagihan NOP ' . $listNop));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }
}

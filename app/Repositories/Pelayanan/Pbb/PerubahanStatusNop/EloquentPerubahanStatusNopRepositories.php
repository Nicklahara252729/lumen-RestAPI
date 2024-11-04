<?php

namespace App\Repositories\Pelayanan\Pbb\PerubahanStatusNop;

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
use App\Traits\Notification;
use App\Traits\Generator;
use App\Traits\Calculation;

/**
 * import models
 */

use App\Models\Pelayanan\Pelayanan\Pelayanan;
use App\Models\Sppt\Sppt;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\DatSubjekPajak\DatSubjekPajak;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\DatOpBumi\DatOpBumi;
use App\Models\DatOpBangunan\DatOpBangunan;

/**
 * import helpers
 */

use App\Libraries\PaginateHelpers;
use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Pbb\PerubahanStatusNop\PerubahanStatusNopRepositories;

class EloquentPerubahanStatusNopRepositories implements PerubahanStatusNopRepositories
{
    use Message, Response, Notification, Generator, Calculation;

    private $pelayanan;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $sppt;
    private $datObjekPajak;
    private $datSubjekPajak;
    private $pembayaranSppt;
    private $datetime;
    private $datOpBumi;
    private $year;
    private $nip;
    private $datOpBangunan;

    public function __construct(
        Pelayanan $pelayanan,
        Sppt $sppt,
        DatObjekPajak $datObjekPajak,
        PembayaranSppt $pembayaranSppt,
        DatSubjekPajak $datSubjekPajak,
        CheckerHelpers $checkerHelpers,
        DatOpBangunan $datOpBangunan,
        DatOpBumi $datOpBumi,
        PaginateHelpers $paginateHelpers
    ) {
        /**
         * initialize model
         */
        $this->pelayanan = $pelayanan;
        $this->sppt = $sppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->datSubjekPajak = $datSubjekPajak;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->datOpBumi = $datOpBumi;
        $this->datOpBangunan = $datOpBangunan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->provinsi  = [globalAttribute()['kdProvinsi'], 'SUMATERA UTARA'];
        $this->kabupaten = [globalAttribute()['kdKota'], 'BINJAI'];
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->year = Carbon::now()->format('Y');
        $this->nip = authAttribute()['nip'];
    }

    /**
     * update status NOP
     */
    public function update($request)
    {
        DB::beginTransaction();
        try {

            /**
             * input for pelayanan
             */
            $nop = $request['nop'];
            $kdProvinsi = substr($nop, 0, 2);
            $kdDati2 = substr($nop, 2, 2);
            $kdKecamatan = substr($nop, 4, 3);
            $kdKelurahan = substr($nop, 7, 3);
            $kdBlok = substr($nop, 10, 3);
            $noUrut = substr($nop, 13, 4);
            $kdJnsOp = substr($nop, 17, 1);

            $pelayananInput['nomor_pelayanan']      = $request['nomor_pelayanan'];
            $pelayananInput['uuid_layanan']         = $request['uuid_layanan'];
            $pelayananInput['uuid_jenis_pelayanan'] = $request['uuid_jenis_pelayanan'];
            $pelayananInput['created_by']           = authAttribute()['id'];
            $pelayananInput['nama_lengkap']         = $request['nama_wp'];
            $pelayananInput['alamat']               = $request['alamat'];
            $pelayananInput['op_kd_provinsi']       = $kdProvinsi;
            $pelayananInput['op_kd_kabupaten']      = $kdDati2;
            $pelayananInput['op_kd_kecamatan']      = $kdKecamatan;
            $pelayananInput['op_kd_kelurahan']      = $kdKelurahan;
            $pelayananInput['op_kd_blok']           = $kdBlok;
            $pelayananInput['no_urut']              = $noUrut;
            $pelayananInput['status_kolektif']      = $kdJnsOp;

            /**
             * save data pelayanan
             */
            $savePelayanan = $this->pelayanan->create($pelayananInput);
            if (!$savePelayanan) :
                throw new \Exception($this->outputMessage('unsaved', 'pelayanan'));
            endif;

            /**
             * validation data
             */
            $checkOpBumi = $this->datOpBumi
                ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = "' . $request['nop'] . '"')
                ->first();
            if (is_null($checkOpBumi)) :
                throw new \Exception($this->outputMessage('not found', 'OP Bumi'));
            endif;

            /**
             * update data
             */
            $update = $this->datOpBumi
                ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = "' . $request['nop'] . '"')
                ->update(['JNS_BUMI' => $request['jenis_objek_pajak']]);
            if (!$update) :
                throw new \Exception($this->outputMessage('update fail', 'status NOP'));
            endif;
            $message = $this->outputMessage('updated', 'status NOP');

            /**
             * remove current sppt
             */
            if ($request['jenis_objek_pajak'] == '4') :
                /**
                 * check sppt
                 */
                $checkSppt = $this->sppt
                    ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = "' . $request['nop'] . '"')
                    ->where('THN_PAJAK_SPPT', $this->year)
                    ->first();
                if (is_null($checkSppt)) :
                    throw new \Exception($this->outputMessage('not found', 'SPPT tahun ' . $this->year));
                endif;

                /**
                 * delete process
                 */
                $deleteSppt = $this->sppt
                    ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = "' . $request['nop'] . '"')
                    ->where('THN_PAJAK_SPPT', $this->year)
                    ->delete();
                if (!$deleteSppt) :
                    throw new \Exception($this->outputMessage('delete', 'SPPT yang sedang berjalan'));
                endif;

                $message = $this->outputMessage('fasum', $request['nop']);
            endif;

            DB::commit();
            $response  = $this->success($message);
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * all record
     */
    public function data($pageSize)
    {
        try {

            /**
             * data perubahan status nop
             */
            $data = $this->pelayanan->select(
                "uuid_pelayanan",
                "nomor_pelayanan",
                "nama_lengkap",
                "alamat",
            )
                ->selectRaw("DATE_FORMAT(pelayanan.created_at, '%d/%m/%Y') AS tanggal")
                ->selectRaw('(SELECT name FROM users WHERE uuid_user = pelayanan.created_by) AS pendaftar')
                ->join("jenis_layanan", "pelayanan.uuid_jenis_pelayanan", "=", "jenis_layanan.uuid_jenis_layanan")
                ->where('jenis_layanan', 'perubahan status nop')
                ->orderBy('pelayanan.id', 'desc')
                ->get();

            if ($pageSize != 'all') :
                $collectionObject = collect($data);
                $pageSize = is_null($pageSize) ? 10 : $pageSize;
                $dataPaginate = $this->paginateHelpers->paginate($collectionObject, $pageSize);
            else :
                $dataPaginate = $data;
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
}

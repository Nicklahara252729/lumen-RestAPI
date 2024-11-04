<?php

namespace App\Repositories\Pelayanan\Pbb\Mutasi;

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
use App\Traits\Calculation;

/**
 * import models
 */

use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\Sppt\Sppt;
use App\Models\DatSubjekPajak\DatSubjekPajak;
use App\Models\Pelayanan\Pelayanan\Pelayanan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Pbb\Mutasi\MutasiRepositories;

class EloquentMutasiRepositories implements MutasiRepositories
{
    use Message, Response, Notification, Calculation;

    private $sppt;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $year;
    private $datObjekPajak;
    private $datSubjekPajak;
    private $pelayanan;

    public function __construct(
        Sppt $sppt,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers,
        DatObjekPajak $datObjekPajak,
        Pelayanan $pelayanan,
        DatSubjekPajak $datSubjekPajak
    ) {
        /**
         * initialize model
         */
        $this->sppt = $sppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->datSubjekPajak = $datSubjekPajak;
        $this->pelayanan = $pelayanan;

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
        $this->year      = Carbon::now()->format('Y');
    }

    /**
     * store mutasi pajak
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {

            /**
             * get objek pajak
             */
            $getObjekPajak = $this->datObjekPajak->select('TOTAL_LUAS_BUMI', 'TOTAL_LUAS_BNG', 'SUBJEK_PAJAK_ID')
                ->whereRaw('CONCAT(dat_objek_pajak.KD_PROPINSI, dat_objek_pajak.KD_DATI2, dat_objek_pajak.KD_KECAMATAN, dat_objek_pajak.KD_KELURAHAN, dat_objek_pajak.KD_BLOK, dat_objek_pajak.NO_URUT, dat_objek_pajak.KD_JNS_OP) = ?', [$request['nop']])
                ->first();
            if (is_null($getObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'objek pajak'));
            endif;

            /**
             * input for pelayanan
             */
            $pelayananInput['nomor_pelayanan']      = $request['nomor_pelayanan'];
            $pelayananInput['uuid_layanan']         = $request['uuid_layanan'];
            $pelayananInput['uuid_jenis_pelayanan'] = $request['uuid_jenis_pelayanan'];
            $pelayananInput['created_by']           = authAttribute()['id'];
            $pelayananInput['status_verifikasi']    = 4;

            /**
             * save data pelayanan
             */
            $savePelayanan = $this->pelayanan->create($pelayananInput);
            if (!$savePelayanan) :
                throw new \Exception($this->outputMessage('unsaved', 'pelayanan'));
            endif;

            /**
             * hitung njop pbb
             */
            $paramNjopPbb = [
                'luas_bumi_lama' => $getObjekPajak->TOTAL_LUAS_BUMI,
                'luas_bangunan_lama' => $getObjekPajak->TOTAL_LUAS_BNG,
                'luas_bumi_baru' => $request['luas_bumi'],
                'luas_bangunan_baru' => $request['luas_bangunan'],
                'njop_bumi' => $request['njop_bumi'],
                'njop_bangunan' => $request['njop_bangunan']
            ];
            $njopPbb = $this->njopPbb($paramNjopPbb);

            /**
             * update objek pajak
             */
            $inputOp = [
                'JALAN_OP' => $request['jalan_op'],
                'BLOK_KAV_NO_OP' => $request['blok_op'],
                'RW_OP' => $request['rw_op'],
                'RT_OP' => $request['rt_op'],
                'TOTAL_LUAS_BUMI' => $request['luas_bumi'],
                'TOTAL_LUAS_BNG' => $request['luas_bangunan'],
                'NJOP_BUMI' => $njopPbb['njopTanah'],
                'NJOP_BNG' => $njopPbb['njopBangunan'],
                'SUBJEK_PAJAK_ID'     => $request['ktp']
            ];
            $checkObjekPajak = $this->checkerHelpers->datObjekPajakChecker($inputOp);
            if (is_null($checkObjekPajak)) :
                $updateObjekPajak = $this->datObjekPajak->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = ?', [$request['nop']])
                    ->update($inputOp);
                if (!$updateObjekPajak) :
                    throw new \Exception($this->outputMessage('update fail', 'Objek Pajak'));
                endif;
            endif;

            /**
             * update objek pajak
             */
            $inputSp = [
                'NM_WP' => $request['nama_wp'],
                'JALAN_WP' => $request['jalan_wp'],
                'BLOK_KAV_NO_WP' => $request['blok_wp'],
                'RT_WP' => $request['rt_wp'],
                'RW_WP' => $request['rw_wp'],
                'KELURAHAN_WP' => $request['kelurahan'],
                'KOTA_WP' => $request['kota'],
                'KD_POS_WP' => $request['kode_pos'],
                'TELP_WP' => $request['telp'],
                'NPWP' => $request['npwp'],
                'STATUS_PEKERJAAN_WP' => $request['status_pekerjaan'],
                'SUBJEK_PAJAK_ID'     => $request['ktp']
            ];
            $checkSubjekPajak = $this->checkerHelpers->datSubjekPajakChecker($inputSp);
            if (is_null($checkSubjekPajak)) :
                $updateDatSubjekPajak = $this->datSubjekPajak->where(['SUBJEK_PAJAK_ID' => $getObjekPajak->SUBJEK_PAJAK_ID])
                    ->update($inputSp);
                if (!$updateDatSubjekPajak) :
                    throw new \Exception($this->outputMessage('update fail', 'Subjek Pajak ' . $getObjekPajak->SUBJEK_PAJAK_ID));
                endif;
            endif;

            /**
             * update sppt
             */
            $inputSppt = [
                'LUAS_BUMI_SPPT' => $request['luas_bumi'],
                'LUAS_BNG_SPPT' => $request['luas_bangunan'],
                'NJOP_BUMI_SPPT' => $njopPbb['njopTanah'],
                'NJOP_BNG_SPPT' => $njopPbb['njopBangunan'],
                'NJOP_SPPT' => $njopPbb['njopSppt'],
                'NM_WP_SPPT' => $request['nama_wp'],
                'JLN_WP_SPPT' => $request['jalan_wp'],
                'BLOK_KAV_NO_WP_SPPT' => $request['blok_wp'],
                'RT_WP_SPPT' => $request['rt_wp'],
                'RW_WP_SPPT' => $request['rw_wp'],
                'KELURAHAN_WP_SPPT' => $request['kelurahan'],
                'KOTA_WP_SPPT' => $request['kota'],
                'KD_POS_WP_SPPT' => $request['kode_pos'],
                'NPWP_SPPT' => $request['npwp']
            ];

            $querySppt = $this->sppt->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = ?', [$request['nop']])->where('THN_PAJAK_SPPT', $request['tahun']);
            $checkSppt = $querySppt->where($inputSppt)->first();
            if (!is_null($checkSppt)) :
                $updateSppt = $querySppt->update($inputSppt);
                if (!$updateSppt) :
                    throw new \Exception($this->outputMessage('update fail', 'SPPT'));
                endif;
            endif;

            DB::commit();
            $response  = $this->success($this->outputMessage('updated', 'objek pajak dengan NOP ' . $request['nop']));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }

        return $response;
    }

    /**
     * autocomplete objek
     */
    public function autocompleteObjek($nop, $tahun)
    {
        try {

            $data = $this->datObjekPajak->select(
                'JALAN_OP AS alamat',
                'BLOK_KAV_NO_OP AS blok',
                'TOTAL_LUAS_BUMI AS luas_bumi',
                'TOTAL_LUAS_BNG AS luas_bangunan',
                'NJOP_BUMI AS njop_bumi',
                'NJOP_BNG AS njop_bangunan',
                'KD_STATUS_WP AS status_wp',
                'NM_WP_SPPT as nama_wp',
                'sppt.KD_KLS_BNG as kode_kelas_bangunan',
            )
                ->selectRaw('CAST(NILAI_PER_M2_BNG AS UNSIGNED) AS njop_bangunan_permeter')
                ->selectRaw("CASE WHEN RW_OP IS NULL THEN '-' ELSE RW_OP END AS rw")
                ->selectRaw("CASE WHEN RT_OP IS NULL THEN '-' ELSE RT_OP END AS rt")
                ->selectRaw("'SUMATERA UTARA' AS provinsi")
                ->selectRaw("'KOTA BINJAI' AS kota")
                ->selectSub(function ($query) {
                    $query->select('NM_KECAMATAN')
                        ->from('ref_kecamatan')
                        ->whereColumn('ref_kecamatan.KD_KECAMATAN', 'dat_objek_pajak.KD_KECAMATAN')
                        ->limit(1);
                }, 'kecamatan')
                ->selectSub(function ($query) {
                    $query->select('NM_KELURAHAN')
                        ->from('ref_kelurahan')
                        ->whereColumn('ref_kelurahan.KD_KECAMATAN', 'dat_objek_pajak.KD_KECAMATAN')
                        ->whereColumn('ref_kelurahan.KD_KELURAHAN', 'dat_objek_pajak.KD_KELURAHAN')
                        ->limit(1);
                }, 'kelurahan')
                ->join('sppt', function ($join) {
                    $join->on('sppt.KD_PROPINSI', '=', 'dat_objek_pajak.KD_PROPINSI')
                        ->on('sppt.KD_DATI2', '=', 'dat_objek_pajak.KD_DATI2')
                        ->on('sppt.KD_KECAMATAN', '=', 'dat_objek_pajak.KD_KECAMATAN')
                        ->on('sppt.KD_KELURAHAN', '=', 'dat_objek_pajak.KD_KELURAHAN')
                        ->on('sppt.KD_BLOK', '=', 'dat_objek_pajak.KD_BLOK')
                        ->on('sppt.NO_URUT', '=', 'dat_objek_pajak.NO_URUT')
                        ->on('sppt.KD_JNS_OP', '=', 'dat_objek_pajak.KD_JNS_OP');
                })
                ->join('kelas_bangunan', 'sppt.KD_KLS_BNG', '=', 'kelas_bangunan.KD_KLS_BNG')
                ->whereRaw('CONCAT(dat_objek_pajak.KD_PROPINSI, dat_objek_pajak.KD_DATI2, dat_objek_pajak.KD_KECAMATAN, dat_objek_pajak.KD_KELURAHAN, dat_objek_pajak.KD_BLOK, dat_objek_pajak.NO_URUT, dat_objek_pajak.KD_JNS_OP) = ?', [$nop])
                ->where('sppt.THN_PAJAK_SPPT', '=', $tahun)
                ->first();
            if (is_null($data)) :
                throw new \Exception($this->outputMessage('not found', 'Objek atau Subjek'));
            endif;

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * autocomplete subjek
     */
    public function autocompleteSubjek($nop, $tahun)
    {
        try {

            $data = $this->datSubjekPajak->select(
                'NM_WP AS nama_wp',
                'JALAN_WP AS alamat',
                'BLOK_KAV_NO_WP AS blok',
                'KELURAHAN_WP AS kelurahan',
                'KOTA_WP AS kota',
                'STATUS_PEKERJAAN_WP AS status_pekerjaan',
                'dat_subjek_pajak.SUBJEK_PAJAK_ID AS ktp',
                'ref_pekerjaan.nama AS nama_pekerjaan'
            )
                ->selectRaw("'SUMATERA UTARA' AS provinsi")
                ->selectRaw("CASE WHEN RW_WP IS NULL THEN '-' ELSE RW_WP END AS rw")
                ->selectRaw("CASE WHEN RT_WP IS NULL THEN '-' ELSE RT_WP END AS rt")
                ->selectRaw("CASE WHEN KD_POS_WP IS NULL THEN '-' ELSE KD_POS_WP END AS kode_pos")
                ->selectRaw("CASE WHEN TELP_WP IS NULL THEN '-' ELSE TELP_WP END AS telp")
                ->selectRaw("CASE WHEN NPWP IS NULL THEN '-' ELSE NPWP END AS npwp")
                ->join('dat_objek_pajak', 'dat_subjek_pajak.SUBJEK_PAJAK_ID', '=', 'dat_objek_pajak.SUBJEK_PAJAK_ID')
                ->leftJoin('ref_pekerjaan', 'dat_subjek_pajak.STATUS_PEKERJAAN_WP', '=', 'ref_pekerjaan.kode')
                ->whereRaw('CONCAT(dat_objek_pajak.KD_PROPINSI, dat_objek_pajak.KD_DATI2, dat_objek_pajak.KD_KECAMATAN, dat_objek_pajak.KD_KELURAHAN, dat_objek_pajak.KD_BLOK, dat_objek_pajak.NO_URUT, dat_objek_pajak.KD_JNS_OP) = ?', [$nop])
                ->first();
            if (is_null($data)) :
                throw new \Exception($this->outputMessage('not found', 'Objek atau Subjek'));
            endif;

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

<?php

namespace App\Repositories\Pelayanan\Pbb\PenetapanSppt;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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

use App\Models\Sppt\Sppt;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Pbb\PenetapanSppt\PenetapanSpptRepositories;

class EloquentPenetapanSpptRepositories implements PenetapanSpptRepositories
{
    use Message, Response, Generator, Calculation;

    private $checkerHelpers;
    private $paginateHelpers;
    private $sppt;
    private $year;
    private $datetime;

    public function __construct(
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers,
        Sppt $sppt
    ) {
        /**
         * initialize model
         */
        $this->sppt = $sppt;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->year = Carbon::now()->format('Y');
    }

    /**
     * store data
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $kdProvinsi  = substr($request['nop'], 0, 2);
            $kdKabupaten = substr($request['nop'], 2, 2);
            $kdKecamatan = substr($request['nop'], 4, 3);
            $kdKelurahan = substr($request['nop'], 7, 3);
            $kdBlok      = substr($request['nop'], 10, 3);
            $noUrut      = substr($request['nop'], 13, 4);
            $kdJenisOp   = substr($request['nop'], 17, 1);

            $where = [
                'KD_PROPINSI'  => $kdProvinsi,
                'KD_DATI2'     => $kdKabupaten,
                'KD_KECAMATAN' => $kdKecamatan,
                'KD_KELURAHAN' => $kdKelurahan,
                'KD_BLOK'      => $kdBlok,
                'NO_URUT'      => $noUrut,
                'KD_JNS_OP'    => $kdJenisOp
            ];

            $checkSppt = $this->sppt->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $request['nop'] . '"')
                ->where(['THN_PAJAK_SPPT' => $request['tahun']])
                ->first();
            if (!is_null($checkSppt)) :
                throw new \Exception($this->outputMessage('exists', 'SPPT'));
            endif;

            /**
             * get data objek pajak
             */
            $getDatObjekPajak = $this->checkerHelpers->datObjekPajakChecker($where);
            if (is_null($getDatObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'objek pajak sppt'));
            endif;

            /**
             * get data subjek pajak
             */
            $getDatSubjekPajak = $this->checkerHelpers->datSubjekPajakChecker(['SUBJEK_PAJAK_ID' => $getDatObjekPajak->SUBJEK_PAJAK_ID]);
            if (is_null($getDatSubjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'subjek pajak'));
            endif;

            /**
             * get data op Bumi
             */
            $getDatOpBumi      = $this->checkerHelpers->datOpBumiChecker($where);
            if (is_null($getDatOpBumi)) :
                throw new \Exception($this->outputMessage('not found', 'OP Bumi'));
            endif;

            /**
             * get jumlah bangunan
             */
            $getDatOpBng      = $this->checkerHelpers->datOpBangunanChecker($where);
            $jumlahBangunan   = is_null($getDatOpBng) ? 0 : 1;

            /**
             * njop bumi, njop sppt, pbb terhutang, pbb harus dibayar new sppt
             */
            $luasBumi          = $getDatObjekPajak->TOTAL_LUAS_BUMI;
            $luasBng           = $getDatObjekPajak->TOTAL_LUAS_BNG;
            $njopBumi          = $getDatObjekPajak->NJOP_BUMI;
            $njopBangunan      = $getDatObjekPajak->NJOP_BNG;
            $nilaiBumiPermeter = $njopBumi / $luasBumi;
            $nilaiBngPermeter  = $njopBangunan / $luasBng;
            $faktorPengurang   = $this->faktorPengurang();
            $njopSppt          = $this->njopSppt($njopBumi, $njopBangunan);
            $njoptkpSppt       = $this->njoptkp($jumlahBangunan);
            $pbbTerhutang      = $this->pbbTerhutang($njopSppt, $njoptkpSppt);
            $pbbHarusDibayar   = $this->pbbHarusDibayar($pbbTerhutang, $faktorPengurang);

            /**
             * get kelas bangunan
             */
            $kdKelasBng      = 'XXX';
            $thnAwalKelasBng = '1986';
            $getKelasBangunan   = $this->checkerHelpers->kelasBangunanChecker($nilaiBngPermeter);
            if (!is_null($getKelasBangunan)) :
                $kdKelasBng      = $getKelasBangunan->KD_KLS_BNG;
                $thnAwalKelasBng = $getKelasBangunan->THN_AWAL_KLS_BNG;
            endif;

            /**
             * get kelas tanah
             */
            $kdKelasTanah      = 'XXX';
            $thnAwalKelasTanah = '1986';
            $getKelasTanah   = $this->checkerHelpers->kelasBumiChecker($nilaiBumiPermeter);
            if (!is_null($getKelasTanah)) :
                $kdKelasTanah      = $getKelasTanah->KD_KLS_TANAH;
                $thnAwalKelasTanah = $getKelasTanah->THN_AWAL_KLS_TANAH;
            endif;

            /**
             * input for sppt
             */
            $spptInput['KD_PROPINSI']               = $kdProvinsi;
            $spptInput['KD_DATI2']                  = $kdKabupaten;
            $spptInput['KD_KECAMATAN']              = $kdKecamatan;
            $spptInput['KD_KELURAHAN']              = $kdKelurahan;
            $spptInput['KD_BLOK']                   = $kdBlok;
            $spptInput['NO_URUT']                   = $noUrut;
            $spptInput['KD_JNS_OP']                 = $kdJenisOp;
            $spptInput['THN_PAJAK_SPPT']            = $this->year;
            $spptInput['SIKLUS_SPPT']               = 1;
            $spptInput['KD_KANWIL_BANK']            = '01';
            $spptInput['KD_KPPBB_BANK']             = '07';
            $spptInput['KD_BANK_TUNGGAL']           = '01';
            $spptInput['KD_BANK_PERSEPSI']          = '02';
            $spptInput['KD_TP']                     = '01';
            $spptInput['NM_WP_SPPT']                = $getDatSubjekPajak->NM_WP;
            $spptInput['JLN_WP_SPPT']               = $getDatSubjekPajak->JALAN_WP;
            $spptInput['BLOK_KAV_NO_WP_SPPT']       = $getDatSubjekPajak->BLOK_KAV_NO_WP;
            $spptInput['RW_WP_SPPT']                = $getDatSubjekPajak->RW_WP;
            $spptInput['RT_WP_SPPT']                = $getDatSubjekPajak->RT_WP;
            $spptInput['KELURAHAN_WP_SPPT']         = $getDatSubjekPajak->KELURAHAN_WP;
            $spptInput['KOTA_WP_SPPT']              = $getDatSubjekPajak->KOTA_WP;
            $spptInput['KD_POS_WP_SPPT']            = $getDatSubjekPajak->KD_POS_WP;
            $spptInput['NPWP_SPPT']                 = $getDatSubjekPajak->NPWP;
            $spptInput['NO_PERSIL_SPPT']            = null;
            $spptInput['KD_KLS_TANAH']              = $kdKelasTanah;
            $spptInput['THN_AWAL_KLS_TANAH']        = $thnAwalKelasTanah;
            $spptInput['KD_KLS_BNG']                = $kdKelasBng;
            $spptInput['THN_AWAL_KLS_BNG']          = $thnAwalKelasBng;
            $spptInput['TGL_JATUH_TEMPO_SPPT']      = $this->year . '-09-30';
            $spptInput['LUAS_BUMI_SPPT']            = $luasBumi;
            $spptInput['LUAS_BNG_SPPT']             = $luasBng;
            $spptInput['NJOP_BUMI_SPPT']            = $njopBumi;
            $spptInput['NJOP_BNG_SPPT']             = $njopBangunan;
            $spptInput['NJOP_SPPT']                 = $njopSppt;
            $spptInput['NJOPTKP_SPPT']              = $njoptkpSppt; // jika ada bangunan diisi 10 jt jika tidak diisi 0
            $spptInput['NJKP_SPPT']                 = 0;
            $spptInput['PBB_TERHUTANG_SPPT']        = $pbbHarusDibayar;
            $spptInput['FAKTOR_PENGURANG_SPPT']     = 0;
            $spptInput['PBB_YG_HARUS_DIBAYAR_SPPT'] = $pbbHarusDibayar;
            $spptInput['STATUS_PEMBAYARAN_SPPT']    = 0;
            $spptInput['STATUS_TAGIHAN_SPPT']       = 0;
            $spptInput['STATUS_CETAK_SPPT']         = 0;
            $spptInput['TGL_TERBIT_SPPT']           = $this->datetime;
            $spptInput['TGL_CETAK_SPPT']            = $this->datetime;
            $spptInput['NIP_PENCETAK_SPPT']         = authAttribute()['nip'];

            /**
             * save new sppt
             */
            $saveNewSppt = $this->sppt->insert($spptInput);
            if (!$saveNewSppt) :
                throw new \Exception($this->outputMessage('unsaved', 'SPPT'));
            endif;

            /**
             * set response
             */
            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'SPPT'));
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

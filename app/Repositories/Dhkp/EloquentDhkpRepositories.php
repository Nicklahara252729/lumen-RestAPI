<?php

namespace App\Repositories\Dhkp;

/**
 * import component
 */

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Generator;

/**
 * import models
 */

use App\Models\Sppt\Sppt;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\DatObjekPajak\DatObjekPajak;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Dhkp\DhkpRepositories;

class EloquentDhkpRepositories implements DhkpRepositories
{
    use Message, Response, Generator;

    private $sppt;
    private $pembayaranSppt;
    private $datObjekPajak;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $year;
    private $spptAsS;

    public function __construct(
        Sppt $sppt,
        PembayaranSppt $pembayaranSppt,
        DatObjekPajak $datObjekPajak,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers
    ) {
        /**
         * initialize model
         */
        $this->sppt = $sppt;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->spptAsS = DB::table('sppt AS s');

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->provinsi  = globalAttribute()['kdProvinsi'];
        $this->kabupaten = globalAttribute()['kdKota'];
        $this->year = Carbon::now()->format('Y');
    }

    /**
     * data
     */
    public function data($request)
    {
        try {

            /**
             * set where condition
             */
            if (($request->kdKecamatan == 'all' || empty($request->kdKecamatan)) && ($request->kdKelurahan == 'all' || empty($request->kdKelurahan))) :
                $where = ['THN_PAJAK_SPPT' => $request->tahun];
            elseif ($request->kdKecamatan != 'all' && ($request->kdKelurahan == 'all' || empty($request->kdKelurahan))) :
                $where = [
                    'KD_KECAMATAN' => $request->kdKecamatan,
                    'THN_PAJAK_SPPT' => $request->tahun
                ];
            else :
                $where = [
                    'KD_KECAMATAN' => $request->kdKecamatan,
                    'KD_KELURAHAN' => $request->kdKelurahan,
                    'THN_PAJAK_SPPT' => $request->tahun
                ];
            endif;

            /**
             * set where condition for jenis dhkp
             */
            if ($request->jenisDhkp == '123') :
                $jenisDhkp  = '(SELECT(NJOP_BUMI_SPPT + NJOP_BNG_SPPT)) < 1000000000';
            elseif ($request->jenisDhkp == '45') :
                $jenisDhkp = '(SELECT(NJOP_BUMI_SPPT + NJOP_BNG_SPPT)) >= 1000000000';
            elseif ($request->jenisDhkp == '12345') :
                $jenisDhkp = '(SELECT(NJOP_BUMI_SPPT + NJOP_BNG_SPPT)) >= 0';
            else :
                throw new \Exception($this->outputMessage('not found', 'DHKP'));
            endif;

            $dataSppt = $this->spptAsS->select([
                's.KD_PROPINSI',
                's.KD_DATI2',
                's.KD_KECAMATAN',
                's.KD_KELURAHAN',
                's.KD_BLOK',
                's.NO_URUT',
                's.KD_JNS_OP',
                's.NM_WP_SPPT',
                's.THN_PAJAK_SPPT',
                's.PBB_YG_HARUS_DIBAYAR_SPPT',
                's.TGL_JATUH_TEMPO_SPPT',
                's.KD_KLS_TANAH',
                's.KD_KLS_BNG',
                's.LUAS_BUMI_SPPT',
                's.LUAS_BNG_SPPT',
                's.NJOP_BUMI_SPPT',
                's.NJOP_BNG_SPPT',
                's.NJOP_SPPT',
                's.NJOPTKP_SPPT',
                's.NJKP_SPPT',
                's.PBB_TERHUTANG_SPPT',
                's.FAKTOR_PENGURANG_SPPT',
            ])
                // ->addSelect(DB::raw('(SELECT COUNT(*) FROM sppt WHERE CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = CONCAT(s.KD_PROPINSI, s.KD_DATI2, s.KD_KECAMATAN, s.KD_KELURAHAN, s.KD_BLOK, s.NO_URUT, s.KD_JNS_OP)
                //  AND THN_PAJAK_SPPT <= ' . $this->year . ' AND STATUS_PEMBAYARAN_SPPT = 0) AS total'))
                ->where($where)
                ->whereRaw($jenisDhkp)
                ->get();

            $data = [];
            foreach ($dataSppt as $key => $value) :

                /**
                 * hitung tahun tunggakan
                 */
                // $nop = $value->KD_PROPINSI . $value->KD_DATI2 . $value->KD_KECAMATAN . $value->KD_KELURAHAN . $value->KD_BLOK . $value->NO_URUT . $value->KD_JNS_OP;
                // $tahunTunggakan = $this->sppt->select(DB::raw('COUNT(*) AS total'))
                //     ->whereRaw('CONCAT(KD_PROPINSIs, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = ' . $nop . ' AND THN_PAJAK_SPPT <= ' . $this->year . ' AND STATUS_PEMBAYARAN_SPPT = 0')
                //     ->first();

                $getObjekPajak = $this->datObjekPajak->select('JALAN_OP')->where([
                    'KD_PROPINSI' => $value->KD_PROPINSI,
                    'KD_DATI2' => $value->KD_DATI2,
                    'KD_KECAMATAN' => $value->KD_KECAMATAN,
                    'KD_KELURAHAN' => $value->KD_KELURAHAN,
                    'KD_BLOK' => $value->KD_BLOK,
                    'NO_URUT' => $value->NO_URUT,
                    'KD_JNS_OP' => $value->KD_JNS_OP,
                ])->first();
                $set = [
                    'NOP' => $this->nop($value->KD_KECAMATAN, $value->KD_KELURAHAN, $value->KD_BLOK, $value->NO_URUT, $value->KD_JNS_OP),
                    'NM_WP_SPPT' => $value->NM_WP_SPPT,
                    'THN_PAJAK_SPPT' => $value->THN_PAJAK_SPPT,
                    'PBB_YG_HARUS_DIBAYAR_SPPT' => $value->PBB_YG_HARUS_DIBAYAR_SPPT,
                    'TGL_JATUH_TEMPO_SPPT' => $value->TGL_JATUH_TEMPO_SPPT,
                    'KD_KLS_TANAH' => $value->KD_KLS_TANAH,
                    'KD_KLS_BNG' => $value->KD_KLS_BNG,
                    'LUAS_BUMI_SPPT' => $value->LUAS_BUMI_SPPT,
                    'LUAS_BNG_SPPT' => $value->LUAS_BNG_SPPT,
                    'NJOP_BUMI_SPPT' => $value->NJOP_BUMI_SPPT,
                    'NJOP_BNG_SPPT' => $value->NJOP_BNG_SPPT,
                    'NJOP_SPPT' => $value->NJOP_SPPT,
                    'NJOPTKP_SPPT' => $value->NJOPTKP_SPPT,
                    'NJKP_SPPT' => $value->NJKP_SPPT,
                    'PBB_TERHUTANG_SPPT' => $value->PBB_TERHUTANG_SPPT,
                    'FAKTOR_PENGURANG_SPPT' => $value->FAKTOR_PENGURANG_SPPT,
                    'JALAN_OP' => $getObjekPajak->JALAN_OP,
                    // 'TAHUN_TUNGGAKAN' => $value->total
                ];
                array_push($data, $set);
            endforeach;

            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

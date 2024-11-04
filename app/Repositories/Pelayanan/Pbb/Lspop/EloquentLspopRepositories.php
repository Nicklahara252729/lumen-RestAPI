<?php

namespace App\Repositories\Pelayanan\Pbb\Lspop;

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
use App\Traits\CalculateLspop;

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
use App\Models\Pelayanan\Lspop\Lspop;
use App\Models\Sppt\SpptNopInduk;
use App\Models\DatObjekPajak\DatObjekPajakNopInduk;

/**
 * import helpers
 */

use App\Libraries\PaginateHelpers;
use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Pbb\Lspop\LspopRepositories;

class EloquentLspopRepositories implements LspopRepositories
{
    use Message, Response, Notification, Generator, Calculation, CalculateLspop;

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
    private $lspop;
    private $spptNopInduk;
    private $datObjekPajakNopInduk;

    public function __construct(
        Pelayanan $pelayanan,
        Sppt $sppt,
        DatObjekPajak $datObjekPajak,
        PembayaranSppt $pembayaranSppt,
        DatSubjekPajak $datSubjekPajak,
        CheckerHelpers $checkerHelpers,
        DatOpBangunan $datOpBangunan,
        DatOpBumi $datOpBumi,
        Lspop $lspop,
        SpptNopInduk $spptNopInduk,
        DatObjekPajakNopInduk $datObjekPajakNopInduk,
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
        $this->lspop = $lspop;
        $this->spptNopInduk = $spptNopInduk;
        $this->datObjekPajakNopInduk = $datObjekPajakNopInduk;

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
     * store pecah NOP
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

            /**
             * get data objek pajak
             */
            $getDatObjekPajak = $this->checkerHelpers->datObjekPajakChecker($where);
            if (is_null($getDatObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'objek pajak sppt induk'));
            endif;

            /**
             * get data subjek pajak
             */
            $getDatSubjekPajak = $this->checkerHelpers->datSubjekPajakChecker(['SUBJEK_PAJAK_ID' => $getDatObjekPajak->SUBJEK_PAJAK_ID]);
            if (is_null($getDatSubjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'subjek pajak induk'));
            endif;

            /**
             * get sppt
             */
            $getSppt = $this->checkerHelpers->spptChecker($where);
            if (is_null($getSppt)) :
                throw new \Exception($this->outputMessage('not found', 'SPPT'));
            endif;

            /**
             * input for pelayanan
             */
            $pelayananInput = collect($request)->only([
                'nomor_pelayanan',
                'uuid_layanan',
                'status_kolektif',
                'uuid_jenis_pelayanan',
                'id_pemohon',
                'nama_lengkap',
                'id_provinsi',
                'id_kabupaten',
                'id_kecamatan',
                'id_kelurahan',
                'alamat',
                'sp_nama_lengkap',
                'sp_alamat',
                'op_alamat',
                'status_verifikasi',
                'created_by'
            ])->toArray();
            $pelayananInput['op_kd_provinsi']    = $kdProvinsi;
            $pelayananInput['op_kd_kabupaten']   = $kdKabupaten;
            $pelayananInput['op_kd_kecamatan']   = $kdKecamatan;
            $pelayananInput['op_kd_kelurahan']   = $kdKelurahan;
            $pelayananInput['op_kd_blok']        = $kdBlok;
            $pelayananInput['no_urut']           = $noUrut;
            $pelayananInput['op_kd_jenis_objek'] = $kdJenisOp;

            /**
             * save data pelayanan
             */
            $savePelayanan = $this->pelayanan->create($pelayananInput);
            if (!$savePelayanan) :
                throw new \Exception($this->outputMessage('unsaved', 'pelayanan'));
            endif;

            /**
             * input value lspop
             */
            $njopBangunan       = 0;
            $totalLuasBangunan  = 0;
            $lspopInput         = [];
            $datOpBangunanInput = [];
            if (isset($request['no_bangunan'])) :

                foreach ($request['no_bangunan'] as $key => $value) :

                    /**
                     * set value for lspop
                     */
                    $setLpop = [
                        'created_at'              => $this->datetime,
                        'updated_at'              => $this->datetime,
                        'nomor_pelayanan'         => $request['nomor_pelayanan'],
                        'no_bangunan'             => $value,
                        'jenis_bangunan'          => $request['jenis_bangunan'][$key],
                        'luas_bangunan'           => $request['luas_bangunan'][$key],
                        'thn_dibangun'            => $request['thn_dibangun'][$key],
                        'jlh_lantai'              => $request['jlh_lantai'][$key],
                        'thn_renovasi'            => $request['thn_renovasi'][$key],
                        'kondisi_bangunan'        => $request['kondisi_bangunan'][$key],
                        'konstruksi'              => $request['konstruksi'][$key],
                        'atap'                    => $request['atap'][$key],
                        'dinding'                 => $request['dinding'][$key],
                        'lantai'                  => $request['lantai'][$key],
                        'langit_langit'           => $request['langit_langit'][$key],
                        'daya_listrik'            => isset($request['daya_listrik'][$key]) ? $request['daya_listrik'][$key] : null,
                        'jumlah_ac_split'         => isset($request['jumlah_ac_split'][$key]) ? $request['jumlah_ac_split'][$key] : null,
                        'jumlah_ac_window'        => isset($request['jumlah_ac_window'][$key]) ? $request['jumlah_ac_window'][$key] : null,
                        'luas_kolam_renang'       => isset($request['luas_kolam_renang'][$key]) ? $request['luas_kolam_renang'][$key] : null,
                        'finishing_kolam'         => isset($request['finishing_kolam'][$key]) ? $request['finishing_kolam'][$key] : null,
                        'jlt_beton_dgn_lampu'     => isset($request['jlt_beton_dgn_lampu'][$key]) ? $request['jlt_beton_dgn_lampu'][$key] : null,
                        'jlt_beton_tanpa_lampu'   => isset($request['jlt_beton_tanpa_lampu'][$key]) ? $request['jlt_beton_tanpa_lampu'][$key] : null,
                        'jlt_aspal_dgn_lampu'     => isset($request['jlt_aspal_dgn_lampu'][$key]) ? $request['jlt_aspal_dgn_lampu'][$key] : null,
                        'jlt_aspal_tanpa_lampu'   => isset($request['jlt_aspal_tanpa_lampu'][$key]) ? $request['jlt_aspal_tanpa_lampu'][$key] : null,
                        'jlt_rumput_dgn_lampu'    => isset($request['jlt_rumput_dgn_lampu'][$key]) ? $request['jlt_rumput_dgn_lampu'][$key] : null,
                        'jlt_rumput_tanpa_lampu'  => isset($request['jlt_rumput_tanpa_lampu'][$key]) ? $request['jlt_rumput_tanpa_lampu'][$key] : null,
                        'panjang_pagar'           => isset($request['panjang_pagar'][$key]) ? $request['panjang_pagar'][$key] : null,
                        'bahan_pagar'             => isset($request['bahan_pagar'][$key]) ? $request['bahan_pagar'][$key] : null,
                        'jlh_pabx'                => isset($request['jlh_pabx'][$key]) ? $request['jlh_pabx'][$key] : null,
                        'ac_sentral'              => isset($request['ac_sentral'][$key]) ? $request['ac_sentral'][$key] : null,
                        'lph_ringan'              => isset($request['lph_ringan'][$key]) ? $request['lph_ringan'][$key] : null,
                        'lph_sedang'              => isset($request['lph_sedang'][$key]) ? $request['lph_sedang'][$key] : null,
                        'lph_berat'               => isset($request['lph_berat'][$key]) ? $request['lph_berat'][$key] : null,
                        'lph_dgn_penutup_lantai'  => isset($request['lph_dgn_penutup_lantai'][$key]) ? $request['lph_dgn_penutup_lantai'][$key] : null,
                        'jlh_lift_penumpang'      => isset($request['jlh_lift_penumpang'][$key]) ? $request['jlh_lift_penumpang'][$key] : null,
                        'jlh_lift_kapsul'         => isset($request['jlh_lift_kapsul'][$key]) ? $request['jlh_lift_kapsul'][$key] : null,
                        'jlh_lift_barang'         => isset($request['jlh_lift_barang'][$key]) ? $request['jlh_lift_barang'][$key] : null,
                        'jlh_eskalator_1'         => isset($request['jlh_eskalator_1'][$key]) ? $request['jlh_eskalator_1'][$key] : null,
                        'jlh_eskalator_2'         => isset($request['jlh_eskalator_2'][$key]) ? $request['jlh_eskalator_2'][$key] : null,
                        'pemadam_hydrant'         => isset($request['pemadam_hydrant'][$key]) ? $request['pemadam_hydrant'][$key] : null,
                        'pemadam_sprinkler'       => isset($request['pemadam_sprinkler'][$key]) ? $request['pemadam_sprinkler'][$key] : null,
                        'pemadam_fire_alarm'      => isset($request['pemadam_fire_alarm'][$key]) ? $request['pemadam_fire_alarm'][$key] : null,
                        'sumur_artesis'           => isset($request['sumur_artesis'][$key]) ? $request['sumur_artesis'][$key] : null,
                    ];
                    array_push($lspopInput, $setLpop);
                    $totalLuasBangunan += $request['luas_bangunan'][$key];
                    $valueCalculateLspop = [
                        'atap'              => $request['atap'][$key],
                        'dinding'           => $request['dinding'][$key],
                        'lantai'            => $request['lantai'][$key],
                        'langit_langit'     => $request['langit_langit'][$key],
                        'kd_jpb'            => $request['jenis_bangunan'][$key],
                        'thn_dbkb_standard' => $request['thn_dibangun'][$key],
                        'tipe_bng'          => $request['luas_bangunan'][$key],
                        'kd_bng_lantai'     => $request['jlh_lantai'][$key],
                        'thn_renovasi'      => $request['thn_renovasi'][$key],
                        'kondisi_bangunan'  => $request['kondisi_bangunan'][$key],
                        'kd_kecamatan'      => $kdKecamatan,
                        'kd_kelurahan'      => $kdKelurahan,
                        'kd_blok'           => $kdBlok,
                        'no_urut'           => $noUrut,
                        'kd_jns_op'         => $kdJenisOp
                    ];
                    $calculateLspop = round($this->hitungLspop($valueCalculateLspop)['njop']);
                    $njopBangunan += $calculateLspop;

                    /**
                     * set value for dat op bangunan
                     */
                    $setOpBangunan  = [
                        'KD_PROPINSI'         => $kdProvinsi,
                        'KD_DATI2'            => $kdKabupaten,
                        'KD_KECAMATAN'        => $kdKecamatan,
                        'KD_KELURAHAN'        => $kdKelurahan,
                        'KD_BLOK'             => $kdBlok,
                        'NO_URUT'             => $noUrut,
                        'KD_JNS_OP'           => $kdJenisOp,
                        'NO_BNG'              => $key,
                        'KD_JPB'              => $request['jenis_bangunan'][$key],
                        'NO_FORMULIR_LSPOP'   => $request['nomor_pelayanan'],
                        'THN_DIBANGUN_BNG'    => $request['thn_dibangun'][$key],
                        'THN_RENOVASI_BNG'    => $request['thn_renovasi'][$key],
                        'LUAS_BNG'            => $totalLuasBangunan,
                        'JML_LANTAI_BNG'      => $request['jlh_lantai'][$key],
                        'KONDISI_BNG'         => $request['kondisi_bangunan'][$key],
                        'JNS_KONSTRUKSI_BNG'  => $request['konstruksi'][$key],
                        'JNS_ATAP_BNG'        => $request['atap'][$key],
                        'KD_DINDING'          => $request['dinding'][$key],
                        'KD_LANTAI'           => $request['lantai'][$key],
                        'KD_LANGIT_LANGIT'    => $request['langit_langit'][$key],
                        'NILAI_SISTEM_BNG'    => $njopBangunan,
                        'JNS_TRANSAKSI_BNG'   => 1,
                        'TGL_PENDATAAN_BNG'   => $this->datetime,
                        'NIP_PENDATA_BNG'     => authAttribute()['nip'],
                        'TGL_PEMERIKSAAN_BNG' => $this->datetime,
                        'NIP_PEMERIKSA_BNG'   => authAttribute()['nip'],
                        'TGL_PEREKAMAN_BNG'   => $this->datetime,
                        'NIP_PEREKAM_BNG'     => authAttribute()['nip'],
                    ];
                    array_push($datOpBangunanInput, $setOpBangunan);
                endforeach;

                /**
                 * save data op bangunan new
                 */
                $saveOpBangunan = $this->datOpBangunan->insert($datOpBangunanInput);
                if (!$saveOpBangunan) :
                    throw new \Exception($this->outputMessage('unsaved', 'op bangunan'));
                endif;
            endif;

            /**
             * input for objek pajak sppt parent
             */
            $opInputSpp['TOTAL_LUAS_BNG']   = $totalLuasBangunan;
            $opInputSpp['NJOP_BNG']         = $njopBangunan;

            /**
             * update data objek pajak sppt parent
             */
            $updateOpParent = $this->datObjekPajak->where($where)->update($opInputSpp);
            if (!$updateOpParent) :
                throw new \Exception($this->outputMessage('update fail', 'objek pajak'));
            endif;

            /**
             * save new lspop
             */
            $saveLspop = $this->lspop->insert($lspopInput);
            if (!$saveLspop) :
                throw new \Exception($this->outputMessage('unsaved', 'LSPOP'));
            endif;

            DB::commit();
            $response  = $this->success($this->outputMessage('saved', 'pendataan LSPOP dengan nomor pelayanan ' . $request['nomor_pelayanan']));
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
     * autocomplete
     */
    public function autocomplete($nop, $tahun)
    {
        try {

            $data = $this->datObjekPajak->select(
                'JALAN_OP AS alamat_op',
                'NM_WP_SPPT AS nama_wp',
                'JLN_WP_SPPT AS alamat_sp'
            )
                ->join('sppt', function ($join) {
                    $join->on('sppt.KD_PROPINSI', '=', 'dat_objek_pajak.KD_PROPINSI')
                        ->on('sppt.KD_DATI2', '=', 'dat_objek_pajak.KD_DATI2')
                        ->on('sppt.KD_KECAMATAN', '=', 'dat_objek_pajak.KD_KECAMATAN')
                        ->on('sppt.KD_KELURAHAN', '=', 'dat_objek_pajak.KD_KELURAHAN')
                        ->on('sppt.KD_BLOK', '=', 'dat_objek_pajak.KD_BLOK')
                        ->on('sppt.NO_URUT', '=', 'dat_objek_pajak.NO_URUT')
                        ->on('sppt.KD_JNS_OP', '=', 'dat_objek_pajak.KD_JNS_OP');
                })
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
}

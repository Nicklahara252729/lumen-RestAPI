<?php

namespace App\Repositories\Pelayanan\Pbb\PecahNop;

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

use App\Repositories\Pelayanan\Pbb\PecahNop\PecahNopRepositories;

class EloquentPecahNopRepositories implements PecahNopRepositories
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

            $generateNoUrut = $this->noUrutSppt($kdKecamatan, $kdKelurahan, $kdBlok);

            /**
             * get data objek pajak
             */
            $getDatObjekPajak = $this->checkerHelpers->datObjekPajakChecker($where);
            if (is_null($getDatObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'objek pajak sppt induk'));
            endif;

            /**
             * save backup objek pajak nop induk
             */
            $saveOpNopInduk = $this->datObjekPajakNopInduk->insert($getDatObjekPajak->toArray());
            if (!$saveOpNopInduk) :
                throw new \Exception($this->outputMessage('unsaved', 'backup objek pajak sppt induk'));
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
             * save sppt nop induk
             */
            $saveSpptNopInduk = $this->spptNopInduk->insert($getSppt->toArray());
            if (!$saveSpptNopInduk) :
                throw new \Exception($this->outputMessage('unsaved', 'backup SPPT nop induk'));
            endif;

            /**
             * input for pelayanan
             */
            $pelayananInput['nomor_pelayanan']      = $request['nomor_pelayanan'];
            $pelayananInput['uuid_layanan']         = $request['uuid_layanan'];
            $pelayananInput['uuid_jenis_pelayanan'] = $request['uuid_jenis_pelayanan'];
            $pelayananInput['created_by']           = authAttribute()['id'];
            $pelayananInput['id_pemohon']           = $request['sp_no_ktp'];
            $pelayananInput['nama_lengkap']         = $request['sp_nm'];
            $pelayananInput['status_verifikasi']    = $request['status_verifikasi'];

            /**
             * save data pelayanan
             */
            $savePelayanan = $this->pelayanan->create($pelayananInput);
            if (!$savePelayanan) :
                throw new \Exception($this->outputMessage('unsaved', 'pelayanan'));
            endif;

            /**
             * input for subjek pajak new sppt
             */
            $subjekPajakInput['SUBJEK_PAJAK_ID']     = $request['sp_no_ktp'];
            $subjekPajakInput['NM_WP']               = $request['sp_nm'];
            $subjekPajakInput['JALAN_WP']            = $request['sp_jalan'];
            $subjekPajakInput['BLOK_KAV_NO_WP']      = isset($request['sp_blok']) ? $request['sp_blok'] : null;
            $subjekPajakInput['RW_WP']               = isset($request['sp_rw']) ? $request['sp_rw'] : null;
            $subjekPajakInput['RT_WP']               = isset($request['sp_rt']) ? $request['sp_rt'] : null;
            $subjekPajakInput['KELURAHAN_WP']        = $request['sp_kelurahan'];
            $subjekPajakInput['KOTA_WP']             = $request['sp_kota'];
            $subjekPajakInput['KD_POS_WP']           = isset($request['sp_kd_pos']) ? $request['sp_kd_pos'] : null;
            $subjekPajakInput['TELP_WP']             = isset($request['sp_telp']) ? $request['sp_telp'] : null;
            $subjekPajakInput['NPWP']                = isset($request['sp_npwp']) ? $request['sp_npwp'] : null;
            $subjekPajakInput['STATUS_PEKERJAAN_WP'] = $request['sp_status_pekerjaan'];

            /**
             * save data subjek pajak new sppt
             */
            $saveSubjekPajak = $this->datSubjekPajak->insert($subjekPajakInput);
            if (!$saveSubjekPajak) :
                throw new \Exception($this->outputMessage('unsaved', 'subjek pajak pecah'));
            endif;

            /**
             * input value lspop
             */
            $totalLuasBangunanParent  = $getDatObjekPajak->TOTAL_LUAS_BNG;
            $njopBangunanParent       = $getDatObjekPajak->NJOP_BNG;
            $njopBangunanNewSppt      = 0;
            $totalLuasBangunanNewSppt = 0;
            $njoptkpSpptNew           = $this->njoptkp(sizeof($request['no_bangunan']));
            $njoptkpSpptParent        = $getSppt->NJOPTKP_SPPT;
            $kdKelasBngParent         = $getSppt->KD_KLS_BNG;
            $thnAwalKelasBngParent    = $getSppt->THN_AWAL_KLS_BNG;
            $lspopInput               = [];
            $datOpBangunanNewInput    = [];
            if ($getDatObjekPajak->TOTAL_LUAS_BNG != 0 && !is_null($getDatObjekPajak->TOTAL_LUAS_BNG) && isset($request['no_bangunan'])) :

                $njoptkpSpptParent        = 0;
                $kdKelasBngParent         = 'XXX';
                $thnAwalKelasBngParent    = '1986';

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
                    $totalLuasBangunanNewSppt += $request['luas_bangunan'][$key];
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
                    $njopBangunanNewSppt += $calculateLspop;

                    /**
                     * set value for dat op bangunan
                     */
                    $setOpBangunan  = [
                        'KD_PROPINSI'         => $kdProvinsi,
                        'KD_DATI2'            => $kdKabupaten,
                        'KD_KECAMATAN'        => $kdKecamatan,
                        'KD_KELURAHAN'        => $kdKelurahan,
                        'KD_BLOK'             => $kdBlok,
                        'NO_URUT'             => $generateNoUrut,
                        'KD_JNS_OP'           => $kdJenisOp,
                        'NO_BNG'              => $key,
                        'KD_JPB'              => $request['jenis_bangunan'][$key],
                        'NO_FORMULIR_LSPOP'   => $request['nomor_pelayanan'],
                        'THN_DIBANGUN_BNG'    => $request['thn_dibangun'][$key],
                        'THN_RENOVASI_BNG'    => $request['thn_renovasi'][$key],
                        'LUAS_BNG'            => $totalLuasBangunanNewSppt,
                        'JML_LANTAI_BNG'      => $request['jlh_lantai'][$key],
                        'KONDISI_BNG'         => $request['kondisi_bangunan'][$key],
                        'JNS_KONSTRUKSI_BNG'  => $request['konstruksi'][$key],
                        'JNS_ATAP_BNG'        => $request['atap'][$key],
                        'KD_DINDING'          => $request['dinding'][$key],
                        'KD_LANTAI'           => $request['lantai'][$key],
                        'KD_LANGIT_LANGIT'    => $request['langit_langit'][$key],
                        'NILAI_SISTEM_BNG'    => $njopBangunanNewSppt,
                        'JNS_TRANSAKSI_BNG'   => 1,
                        'TGL_PENDATAAN_BNG'   => $this->datetime,
                        'NIP_PENDATA_BNG'     => authAttribute()['nip'],
                        'TGL_PEMERIKSAAN_BNG' => $this->datetime,
                        'NIP_PEMERIKSA_BNG'   => authAttribute()['nip'],
                        'TGL_PEREKAMAN_BNG'   => $this->datetime,
                        'NIP_PEREKAM_BNG'     => authAttribute()['nip'],
                    ];
                    array_push($datOpBangunanNewInput, $setOpBangunan);
                endforeach;
                $totalLuasBangunanParent = 0;
                $njopBangunanParent = 0;

                /**
                 * input for op bangunan parent
                 */
                $opBangunanParent['LUAS_BNG']            = 0;
                $opBangunanParent['JML_LANTAI_BNG']      = 0;
                $opBangunanParent['KONDISI_BNG']         = 0;
                $opBangunanParent['JNS_KONSTRUKSI_BNG']  = 0;
                $opBangunanParent['JNS_ATAP_BNG']        = 0;
                $opBangunanParent['KD_DINDING']          = 0;
                $opBangunanParent['KD_LANTAI']           = 0;
                $opBangunanParent['KD_LANGIT_LANGIT']    = 0;
                $opBangunanParent['NILAI_SISTEM_BNG']    = 0;
                $opBangunanParent['JNS_TRANSAKSI_BNG']   = 0;
                $opBangunanParent['TGL_PEMERIKSAAN_BNG'] = $this->datetime;
                $opBangunanParent['NIP_PEMERIKSA_BNG']   = 'BJI';

                /**
                 * update data op bangunan parent
                 */
                $updateOpBangunanParent = $this->datOpBangunan->where($where)->update($opBangunanParent);
                if (!$updateOpBangunanParent) :
                    throw new \Exception($this->outputMessage('update fail', 'op bangunan nop induk'));
                endif;

                /**
                 * save data op bangunan new
                 */
                $saveOpBangunanNew = $this->datOpBangunan->insert($datOpBangunanNewInput);
                if (!$saveOpBangunanNew) :
                    throw new \Exception($this->outputMessage('unsaved', 'op bangunan nop pecah'));
                endif;
            endif;

            /**
             * njop bumi, njop sppt, pbb terhutang, pbb harus dibayar sppt parent
             */
            $luasBumiParent          = $getSppt->LUAS_BUMI_SPPT - $request['luas_bumi'];
            $getKelasBumiParent      = $this->checkerHelpers->kelasBumiChecker(['KD_KLS_TANAH' => $getSppt->KD_KLS_TANAH]);
            $njopBumiParent          = $this->njopBumi($getKelasBumiParent->NILAI_PER_M2_TANAH, $luasBumiParent);
            $njopSpptParent          = $this->njopSppt($njopBumiParent, $njopBangunanParent);
            $pbbTerhutangParent      = $this->pbbTerhutang($njopSpptParent, $njoptkpSpptParent);
            $pbbHarusDibayarParent   = $this->pbbHarusDibayar($pbbTerhutangParent, $getSppt->FAKTOR_PENGURANG_SPPT);

            /**
             * input for objek pajak sppt parent
             */
            $opInputSpptParent['TOTAL_LUAS_BUMI']     = $luasBumiParent;
            $opInputSpptParent['TOTAL_LUAS_BNG']      = $totalLuasBangunanParent;
            $opInputSpptParent['NJOP_BUMI']           = $njopBumiParent;
            $opInputSpptParent['NJOP_BNG']            = $njopBangunanParent;

            /**
             * update data objek pajak sppt parent
             */
            $updateOpParent = $this->datObjekPajak->where($where)->update($opInputSpptParent);
            if (!$updateOpParent) :
                throw new \Exception($this->outputMessage('update fail', 'objek pajak induk'));
            endif;

            /**
             * input for op bumi parent
             */
            $opBumiInputParent['LUAS_BUMI']         = $luasBumiParent;
            $opBumiInputParent['NILAI_SISTEM_BUMI'] = $njopBumiParent;

            /**
             * update data op bumi parent
             */
            $updateOpBumiParent = $this->datOpBumi->where($where)->update($opBumiInputParent);
            if (!$updateOpBumiParent) :
                throw new \Exception($this->outputMessage('update fail', 'op bumi nop induk'));
            endif;

            /**
             * njop bumi, njop sppt, pbb terhutang, pbb harus dibayar new sppt
             */
            $luasBumiNewSppt          = $request['luas_bumi'];
            $getKelasBumiNewSppt      = $this->checkerHelpers->kelasBumiChecker(['KD_KLS_TANAH' => $request['kd_znt']]);
            $njopBumiNewSppt          = $this->njopBumi($getKelasBumiNewSppt->NILAI_PER_M2_TANAH, $luasBumiNewSppt);
            $faktorPengurang          = $this->faktorPengurang();
            $njopSpptNewSppt          = $this->njopSppt($njopBumiNewSppt, $njopBangunanNewSppt);
            $pbbTerhutangNewSppt      = $this->pbbTerhutang($njopSpptNewSppt, $njoptkpSpptNew);
            $pbbHarusDibayarNewSppt   = $this->pbbHarusDibayar($pbbTerhutangNewSppt, $faktorPengurang);

            /**
             * input for objek pajak new sppt
             */
            $objekPajakInput['KD_PROPINSI']         = $kdProvinsi;
            $objekPajakInput['KD_DATI2']            = $kdKabupaten;
            $objekPajakInput['KD_KECAMATAN']        = $kdKecamatan;
            $objekPajakInput['KD_KELURAHAN']        = $kdKelurahan;
            $objekPajakInput['KD_BLOK']             = $kdBlok;
            $objekPajakInput['NO_URUT']             = $generateNoUrut;
            $objekPajakInput['KD_JNS_OP']           = $kdJenisOp;
            $objekPajakInput['SUBJEK_PAJAK_ID']     = $request['sp_no_ktp'];
            $objekPajakInput['NO_FORMULIR_SPOP']    = $request['nomor_pelayanan'];
            $objekPajakInput['NO_PERSIL']           = isset($request['op_no_persil']) ? $request['op_no_persil'] : null;
            $objekPajakInput['JALAN_OP']            = $request['op_jalan'];
            $objekPajakInput['BLOK_KAV_NO_OP']      = isset($request['op_blok']) ? $request['op_blok'] : null;
            $objekPajakInput['RW_OP']               = isset($request['op_rw']);
            $objekPajakInput['RT_OP']               = isset($request['op_rt']) ? $request['op_rt'] : null;
            $objekPajakInput['KD_STATUS_CABANG']    = 0;
            $objekPajakInput['TOTAL_LUAS_BUMI']     = $request['luas_bumi'];
            $objekPajakInput['TOTAL_LUAS_BNG']      = $totalLuasBangunanNewSppt;
            $objekPajakInput['NJOP_BUMI']           = $njopBumiNewSppt;
            $objekPajakInput['NJOP_BNG']            = $njopBangunanNewSppt;
            $objekPajakInput['NIP_PENDATA']         = authAttribute()['nip'];
            $objekPajakInput['TGL_PENDATAAN_OP']    = $this->datetime;
            $objekPajakInput['NIP_PEMERIKSA_OP']    = authAttribute()['nip'];
            $objekPajakInput['TGL_PEMERIKSAAN_OP']  = $this->datetime;
            $objekPajakInput['NIP_PEREKAM_OP']      = authAttribute()['nip'];

            /**
             * save data objek pajak new sppt
             */
            $saveObjekPajak = $this->datObjekPajak->insert($objekPajakInput);
            if (!$saveObjekPajak) :
                throw new \Exception($this->outputMessage('unsaved', 'objek pajak pecah'));
            endif;

            /**
             * save op bumi new
             */
            $opBumiNewInput['KD_PROPINSI']        = $kdProvinsi;
            $opBumiNewInput['KD_DATI2']           = $kdKabupaten;
            $opBumiNewInput['KD_KECAMATAN']       = $kdKecamatan;
            $opBumiNewInput['KD_KELURAHAN']       = $kdKelurahan;
            $opBumiNewInput['KD_BLOK']            = $kdBlok;
            $opBumiNewInput['NO_URUT']            = $generateNoUrut;
            $opBumiNewInput['KD_JNS_OP']          = $kdJenisOp;
            $opBumiNewInput['KD_ZNT']             = $request['kd_znt'];
            $opBumiNewInput['LUAS_BUMI']          = $request['luas_bumi'];
            $opBumiNewInput['JNS_BUMI']           = $request['jns_bumi'];
            $opBumiNewInput['NILAI_SISTEM_BUMI']  = $njopBumiNewSppt;

            /**
             * save data objek pajak bumi new sppt
             */
            $saveObjekPajakBumi = $this->datOpBumi->insert($opBumiNewInput);
            if (!$saveObjekPajakBumi) :
                throw new \Exception($this->outputMessage('unsaved', 'objek pajak bumi pecah'));
            endif;

            /**
             * input for parent sppt
             */
            $spptParentInput['LUAS_BUMI_SPPT']            = $luasBumiParent;
            $spptParentInput['NJOP_BUMI_SPPT']            = $njopBumiParent;
            $spptParentInput['LUAS_BNG_SPPT']             = $totalLuasBangunanParent;
            $spptParentInput['NJOP_BNG_SPPT']             = $njopBangunanParent;
            $spptParentInput['NJOP_SPPT']                 = $njopSpptParent;
            $spptParentInput['PBB_TERHUTANG_SPPT']        = $pbbTerhutangParent;
            $spptParentInput['PBB_YG_HARUS_DIBAYAR_SPPT'] = $pbbHarusDibayarParent;
            $spptParentInput['KD_KLS_BNG']                = $kdKelasBngParent;
            $spptParentInput['THN_AWAL_KLS_BNG']          = $thnAwalKelasBngParent;

            /**
             * update sppt parent
             */
            $updateSpptParent = $this->sppt->where(array_merge($where, ['THN_PAJAK_SPPT' => $getSppt->THN_PAJAK_SPPT]))
                ->update($spptParentInput);
            if (!$updateSpptParent) :
                throw new \Exception($this->outputMessage('update fail', 'sppt induk'));
            endif;

            /**
             * save new lspop
             */
            $saveLspop = $this->lspop->insert($lspopInput);
            if (!$saveLspop) :
                throw new \Exception($this->outputMessage('unsaved', 'LSPOP'));
            endif;

            /**
             * get kelas bangunan
             */
            $kdKelasBngNew      = 'XXX';
            $thnAwalKelasBngNew = '1986';
            $getKelasBangunan   = $this->checkerHelpers->kelasBangunanChecker($njopBangunanNewSppt);
            if (!is_null($getKelasBangunan)) :
                $kdKelasBngNew      = $getKelasBangunan->KD_KLS_BNG;
                $thnAwalKelasBngNew = $getKelasBangunan->THN_AWAL_KLS_BNG;
            endif;

            /**
             * input for new sppt
             */
            $newSpptInput['KD_PROPINSI']               = $kdProvinsi;
            $newSpptInput['KD_DATI2']                  = $kdKabupaten;
            $newSpptInput['KD_KECAMATAN']              = $kdKecamatan;
            $newSpptInput['KD_KELURAHAN']              = $kdKelurahan;
            $newSpptInput['KD_BLOK']                   = $kdBlok;
            $newSpptInput['NO_URUT']                   = $generateNoUrut;
            $newSpptInput['KD_JNS_OP']                 = $kdJenisOp;
            $newSpptInput['THN_PAJAK_SPPT']            = $this->year;
            $newSpptInput['SIKLUS_SPPT']               = 1;
            $newSpptInput['KD_KANWIL_BANK']            = '01';
            $newSpptInput['KD_KPPBB_BANK']             = '07';
            $newSpptInput['KD_BANK_TUNGGAL']           = '01';
            $newSpptInput['KD_BANK_PERSEPSI']          = '02';
            $newSpptInput['KD_TP']                     = '01';
            $newSpptInput['NM_WP_SPPT']                = $request['sp_nm'];
            $newSpptInput['JLN_WP_SPPT']               = $request['sp_jalan'];
            $newSpptInput['BLOK_KAV_NO_WP_SPPT']       = isset($request['sp_blok']) ? $request['sp_blok'] : null;
            $newSpptInput['RW_WP_SPPT']                = isset($request['sp_rw']) ? $request['sp_rw'] : null;
            $newSpptInput['RT_WP_SPPT']                = isset($request['sp_rt']) ? $request['sp_rt'] : null;
            $newSpptInput['KELURAHAN_WP_SPPT']         = $request['sp_kelurahan'];
            $newSpptInput['KOTA_WP_SPPT']              = $request['sp_kota'];
            $newSpptInput['KD_POS_WP_SPPT']            = isset($request['sp_kd_pos']) ? $request['sp_kd_pos'] : null;
            $newSpptInput['NPWP_SPPT']                 = isset($request['sp_npwp']) ? $request['sp_npwp'] : null;
            $newSpptInput['NO_PERSIL_SPPT']            = isset($request['op_no_persil']) ? $request['op_no_persil'] : null;
            $newSpptInput['KD_KLS_TANAH']              = $request['kd_znt'];
            $newSpptInput['THN_AWAL_KLS_TANAH']        = $getKelasBumiNewSppt->THN_AWAL_KLS_TANAH;
            $newSpptInput['KD_KLS_BNG']                = $kdKelasBngNew;
            $newSpptInput['THN_AWAL_KLS_BNG']          = $thnAwalKelasBngNew;
            $newSpptInput['TGL_JATUH_TEMPO_SPPT']      = $getSppt->TGL_JATUH_TEMPO_SPPT;
            $newSpptInput['LUAS_BUMI_SPPT']            = $request['luas_bumi'];
            $newSpptInput['LUAS_BNG_SPPT']             = $totalLuasBangunanNewSppt;
            $newSpptInput['NJOP_BUMI_SPPT']            = $njopBumiNewSppt;
            $newSpptInput['NJOP_BNG_SPPT']             = $njopBangunanNewSppt;
            $newSpptInput['NJOP_SPPT']                 = $njopSpptNewSppt;
            $newSpptInput['NJOPTKP_SPPT']              = $njoptkpSpptNew; // jika ada bangunan diisi 10 jt jika tidak diisi 0
            $newSpptInput['NJKP_SPPT']                 = 0;
            $newSpptInput['PBB_TERHUTANG_SPPT']        = $pbbHarusDibayarNewSppt;
            $newSpptInput['FAKTOR_PENGURANG_SPPT']     = 0;
            $newSpptInput['PBB_YG_HARUS_DIBAYAR_SPPT'] = $pbbHarusDibayarNewSppt;
            $newSpptInput['STATUS_PEMBAYARAN_SPPT']    = 0;
            $newSpptInput['STATUS_TAGIHAN_SPPT']       = 0;
            $newSpptInput['STATUS_CETAK_SPPT']         = 0;
            $newSpptInput['TGL_TERBIT_SPPT']           = $this->datetime;
            $newSpptInput['TGL_CETAK_SPPT']            = $this->datetime;
            $newSpptInput['NIP_PENCETAK_SPPT']         = authAttribute()['nip'];

            /**
             * save new sppt
             */
            $saveNewSppt = $this->sppt->insert($newSpptInput);
            if (!$saveNewSppt) :
                throw new \Exception($this->outputMessage('unsaved', 'sppt baru'));
            endif;

            /**
             * whatsapp notification
             */
            // $getSetting = $this->checkerHelpers->settingChecker('whatsapp notif');
            // if (is_null($getSetting)) :
            //     throw new \Exception($this->outputMessage('not found', 'whatsapp notification setting'));
            // endif;

            // if ($getSetting->description == 'enabled') :

            //     /**
            //      * get data kasubbid
            //      */
            //     $getKasubbid = $this->checkerHelpers->userChecker(['role' => 'kasubbid']);
            //     if (is_null($getKasubbid)) :
            //         throw new \Exception($this->outputMessage('not found', 'Kasubbid'));
            //     endif;

            //     /**
            //      * get tanggal pendaftaran
            //      */
            //     $getCurrentPelayanan = $this->checkerHelpers->pelayananChecker(['nomor_pelayanan' => $request['nomor_pelayanan']]);
            //     $tanggalPendaftaran = Carbon::parse($getCurrentPelayanan->created_at)->locale('id');
            //     $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);

            //     /**
            //      * send notification
            //      */
            //     $message = "Pembetulan SPPT/SKP/STP";
            //     $message .= "\nAtas nama " . $getDatSubjekPajak->NM_WP;
            //     $message .= "\nNo Pelayanan " . $request['nomor_pelayanan'];
            //     $message .= "\n" . $tanggalPendaftaran->format('l, j F Y ; h:i:s ') . "\n\n";
            //     $message .= "\nMohon segera diproses";
            //     $message .= "\n(OPERATOR)";
            //     $message .= "\n _BPKPAD KOTA BINJAI_";
            //     $callBack = $this->whatsapp($getKasubbid->no_hp, $message);

            //     /**
            //      * fail send whatsapp notification
            //      */
            //     if ($callBack->status != true) :
            //         throw new \Exception($this->outputMessage('unsend', 'whatsapp'));
            //     endif;
            // endif;
            DB::commit();
            $response  = $this->success($this->outputMessage('saved', 'pecah nop dengan nomor pelayanan ' . $request['nomor_pelayanan']));
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
     * update status verifikasi
     */
    public function updateStatusVerifikasi($request, $uuidPelayanan)
    {
        DB::beginTransaction();
        try {
            /**
             * validation data
             */
            $getPelayanan   = $this->checkerHelpers->pelayananChecker(["uuid_pelayanan" => $uuidPelayanan]);
            if (is_null($getPelayanan)) :
                throw new \Exception($this->outputMessage('not found', 'pelayanan'));
            endif;

            /**
             * update data
             */
            $request['updated_by'] = authAttribute()['id'];
            $updatePelayanan = $this->pelayanan->where(['uuid_pelayanan' => $uuidPelayanan])->update($request);
            if (!$updatePelayanan) :
                throw new \Exception($this->outputMessage('update fail', 'status verifikasi'));
            endif;

            /**
             * whatsapp notification
             */
            $getSetting = $this->checkerHelpers->settingChecker('whatsapp notif');
            if (is_null($getSetting)) :
                throw new \Exception($this->outputMessage('not found', 'whatsapp notification setting'));
            endif;

            if ($request['status_verifikasi'] == 3 && $getSetting->description == 'enabled') :

                /**
                 * get data kabid
                 */
                $getKabid = $this->checkerHelpers->userChecker(['role' => 'kabid']);
                if (is_null($getKabid)) :
                    throw new \Exception($this->outputMessage('not found', 'Kabid'));
                endif;

                /**
                 * get tanggal pendaftaran
                 */
                $tanggalPendaftaran = Carbon::parse($getPelayanan->created_at)->locale('id');
                $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);

                /**
                 * send whatsapp notification
                 */
                $message = "Pembetulan SPPT/SKP/STP";
                $message .= "\nAtas nama " . $getPelayanan->nama_lengkap;
                $message .= "\nNo Pelayanan " . $getPelayanan->nomor_pelayanan;
                $message .= "\nData telah diverifikasi";
                $message .= "\n" . $tanggalPendaftaran->format('l, j F Y ; h:i:s ') . "\n\n";
                $message .= "\nMohon segera diproses";
                $message .= "\n(KASUBBID)";
                $message .= "\n _BPKPAD KOTA BINJAI_";
                $callBack = $this->whatsapp($getKabid->no_hp, $message);

                /**
                 * fail send whatsapp notification
                 */
                if ($callBack->status != true) :
                    throw new \Exception($this->outputMessage('unsend', 'whatsapp'));
                endif;
            endif;

            if ($request['status_verifikasi'] == 4 && $getSetting->description == 'enabled') :

                /**
                 * get data objek pajak
                 */
                $getDatObjekPajak = $this->checkerHelpers->datObjekPajakChecker(['SUBJEK_PAJAK_ID' => $getPelayanan->id_pemohon]);
                if (is_null($getDatObjekPajak)) :
                    throw new \Exception($this->outputMessage('not found', 'objek pajak'));
                endif;

                /**
                 * get data subjek pajak
                 */
                $getDatSubjekPajak = $this->checkerHelpers->datSubjekPajakChecker(['SUBJEK_PAJAK_ID' => $getPelayanan->id_pemohon]);
                if (is_null($getDatSubjekPajak)) :
                    throw new \Exception($this->outputMessage('not found', 'subjek pajak'));
                endif;

                $target = $getDatSubjekPajak->TELP_WP;
                $nop = $this->nop($getDatObjekPajak->KD_KECAMATAN, $getDatObjekPajak->KD_KELURAHAN, $getDatObjekPajak->KD_BLOK, $getDatObjekPajak->NO_URUT, $getDatObjekPajak->KD_JNS_OP);

                if (!is_null($target) || !empty($target)) :

                    /**
                     * get sppt
                     */
                    $getSppt = $this->checkerHelpers->spptChecker([
                        'KD_KECAMATAN' => $getDatObjekPajak->KD_KECAMATAN,
                        'KD_KELURAHAN' => $getDatObjekPajak->KD_KELURAHAN,
                        'KD_BLOK'      => $getDatObjekPajak->KD_BLOK,
                        'NO_URUT'      => $getDatObjekPajak->NO_URUT,
                        'KD_JNS_OP'    => $getDatObjekPajak->KD_JNS_OP
                    ]);
                    if (is_null($getSppt)) :
                        throw new \Exception($this->outputMessage('not found', 'SPPT'));
                    endif;

                    /**
                     * send whatsapp notification
                     */
                    $message = "Selamat permohonan pecah nop anda telah di setujui ";
                    $message .= "\nNOP anda adalah " . $nop;
                    $message .= "\nLuas tanah " . $getDatObjekPajak->TOTAL_LUAS_BUMI;
                    $message .= "\nLuas bangunan " . $getDatObjekPajak->TOTAL_LUAS_BNG;
                    $message .= "\nPBB yang harus dibayar " . $getSppt->PBB_YG_HARUS_DIBAYAR_SPPT . "\n\n";
                    $message .= "\nTTD";
                    $message .= "\nKABID PBB & BPHTB";
                    $message .= "\nBPKPAD KOTA BINJAI";
                    $callBack = $this->whatsapp($target, $message);

                    /**
                     * fail send whatsapp notification
                     */
                    if ($callBack->status != true) :
                        throw new \Exception($this->outputMessage('unsend', 'whatsapp'));
                    endif;
                endif;

            endif;
            DB::commit();
            $response  = $this->success($this->outputMessage('updated', 'status verifikasi'));
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
     * autocomplete pecah nop
     */
    public function autocomplete($nop)
    {
        try {

            /**
             * get letak objek pajak
             */
            $getLetakOp = $this->datObjekPajak->select(
                'JALAN_OP AS nama_jalan',
                'RT_OP AS rt',
                'RW_OP AS rw',
                'NO_PERSIL AS no_persil',
                'BLOK_KAV_NO_OP AS blok',
                'SUBJEK_PAJAK_ID'
            )
                ->selectRaw('(SELECT NM_KELURAHAN from ref_kelurahan WHERE KD_KECAMATAN = dat_objek_pajak.KD_KECAMATAN AND KD_KELURAHAN = dat_objek_pajak.KD_KELURAHAN) AS kelurahan')
                ->whereRaw('concat(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
                ->first();
            if (is_null($getLetakOp)) :
                throw new \Exception($this->outputMessage('not found', 'Letak Objek Pajak'));
            endif;

            /**
             * get subjek pajak
             */
            $getSubjekPajak = $this->datSubjekPajak->select(
                'SUBJEK_PAJAK_ID AS no_ktp',
                'JALAN_WP AS jalan_wp',
                'BLOK_KAV_NO_WP AS blok',
                'KELURAHAN_WP AS kelurahan',
                'KD_POS_WP AS kode_pos',
                'STATUS_PEKERJAAN_WP AS kode_pekerjaan',
                'NPWP AS npwp',
                'RT_WP AS rt',
                'RW_WP AS rw',
                'KOTA_WP AS kabupaten'

            )
                ->selectRaw('(SELECT nama FROM ref_pekerjaan WHERE kode = dat_subjek_pajak.STATUS_PEKERJAAN_WP) AS pekerjaan')
                ->where("SUBJEK_PAJAK_ID", $getLetakOp->SUBJEK_PAJAK_ID)
                ->first();

            /**
             * get nama wp from sppt
             */
            $getSppt = $this->sppt->select('NM_WP_SPPT')
                ->whereRaw('concat(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
                ->orderByDesc('THN_PAJAK_SPPT')
                ->first();

            /**
             * get data op bumi
             */
            $getDatOpBumi = $this->datOpBumi->select(
                'LUAS_BUMI AS luas_bumi',
                'KD_ZNT AS kode_znt',
                'JNS_BUMI AS jenis_bumi',
            )
                ->selectRaw('(SELECT nm_jpb FROM ref_jpb_tanah WHERE kd_jpb = dat_op_bumi.JNS_BUMI) AS nama_jenis_bumi')
                ->whereRaw('concat(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
                ->first();

            /**
             * get bangunan
             */
            $getBangunan = $this->datOpBangunan->select('NO_BNG AS jumlah_bangunan')
                ->whereRaw('concat(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
                ->first();

            $data = [
                'letak_op' => [
                    'nama_jalan' => is_null($getLetakOp) ? null : $getLetakOp->nama_jalan,
                    'kelurahan' => is_null($getLetakOp) ? null : $getLetakOp->kelurahan,
                    'blok' => is_null($getLetakOp) ? null : $getLetakOp->blok,
                    'rt' => is_null($getLetakOp) ? null : $getLetakOp->rt,
                    'rw' => is_null($getLetakOp) ? null : $getLetakOp->rw,
                    'no_persil' => is_null($getLetakOp) ? null : $getLetakOp->no_persil,
                ],
                'subjek_pajak' => [
                    'no_ktp' => is_null($getSubjekPajak) ? null : $getSubjekPajak->no_ktp,
                    'nama_wp' => is_null($getSppt) ? null : $getSppt->NM_WP_SPPT,
                    'jalan_wp' => is_null($getSubjekPajak) ? null : $getSubjekPajak->jalan_wp,
                    'blok' => is_null($getSubjekPajak) ? null : $getSubjekPajak->blok,
                    'kelurahan' => is_null($getSubjekPajak) ? null : $getSubjekPajak->kelurahan,
                    'kode_pos' => is_null($getSubjekPajak) ? null : $getSubjekPajak->kode_pos,
                    'pekerjaan' => [
                        'kode' => is_null($getSubjekPajak) ? null : $getSubjekPajak->kode_pekerjaan,
                        'keterangan' => is_null($getSubjekPajak) ? null : $getSubjekPajak->pekerjaan,
                    ],
                    'status_wp' => is_null($getSubjekPajak) ? null : $getSubjekPajak->status_wp,
                    'npwp' => is_null($getSubjekPajak) ? null : $getSubjekPajak->npwp,
                    'rt' => is_null($getSubjekPajak) ? null : $getSubjekPajak->rt,
                    'rw' => is_null($getSubjekPajak) ? null : $getSubjekPajak->rw,
                    'kabupaten' => is_null($getSubjekPajak) ? null : $getSubjekPajak->kabupaten,
                ],
                'bumi' => [
                    'luas_bumi' => is_null($getDatOpBumi) ? null : $getDatOpBumi->luas_bumi,
                    'kode_znt' => is_null($getDatOpBumi) ? null : $getDatOpBumi->kode_znt,
                    'jenis_bumi' => [
                        'kode' => is_null($getDatOpBumi) ? null : $getDatOpBumi->jenis_bumi,
                        'keterangan' => is_null($getDatOpBumi) ? null : $getDatOpBumi->nama_jenis_bumi
                    ],
                ],
                'jumlah_bangunan' => is_null($getBangunan) ? null : $getBangunan->jumlah_bangunan
            ];

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * all record
     */
    public function data($pageSize)
    {
        try {

            /**
             * data pecah nop
             */
            $data = $this->pelayanan->select(
                "uuid_pelayanan",
                "nomor_pelayanan",
                "nama_lengkap"
            )
                ->selectRaw("DATE_FORMAT(pelayanan.created_at, '%d/%m/%Y') AS tanggal")
                ->selectRaw('CONCAT(dat_objek_pajak.KD_PROPINSI,dat_objek_pajak.KD_DATI2,dat_objek_pajak.KD_KECAMATAN,dat_objek_pajak.KD_KELURAHAN,dat_objek_pajak.KD_BLOK,dat_objek_pajak.NO_URUT,dat_objek_pajak.KD_JNS_OP) AS nop, JALAN_OP AS alamat')
                ->selectRaw('(SELECT name FROM users WHERE uuid_user = pelayanan.created_by) AS pendaftar')
                ->join("dat_objek_pajak", "pelayanan.id_pemohon", "=", "dat_objek_pajak.SUBJEK_PAJAK_ID")
                ->join("jenis_layanan", "pelayanan.uuid_jenis_pelayanan", "=", "jenis_layanan.uuid_jenis_layanan")
                ->where('jenis_layanan', 'pecah nop')
                ->orderBy('pelayanan.id', 'desc')
                ->get();
            $collectionObject = collect($data);
            $pageSize = is_null($pageSize) ? 10 : $pageSize;
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, $pageSize);

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

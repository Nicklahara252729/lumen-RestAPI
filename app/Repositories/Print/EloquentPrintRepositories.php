<?php

namespace App\Repositories\Print;

/**
 * import component
 */

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use NumberFormatter;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Generator;
use App\Traits\Converter;
use App\Traits\Calculation;
use App\Traits\Briva;
use App\Traits\Bphtb;

/**
 * import models
 */

use App\Models\User\User;
use App\Models\Pelayanan\Pelayanan\Pelayanan;
use App\Models\Sppt\Sppt;
use App\Models\Region\Kecamatan\Kecamatan;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\PbbMinimal\PbbMinimal;
use App\Models\Skpdkb\Skpdkb;
use App\Models\DatOpBumi\DatOpBumi;
use App\Models\Pelayanan\PelayananBphtb\PelayananBphtb;
use App\Models\DatSubjekPajak\DatSubjekPajak;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Print\PrintRepositories;
use App\Repositories\Pelayanan\Pbb\Pendaftaran\PendaftaranRepositories;

class EloquentPrintRepositories implements PrintRepositories
{
    use Message, Response, Generator, Converter, Calculation, Briva, Bphtb;

    private $pendaftaranRepositories;
    private $user;
    private $checkerHelpers;
    private $pelayanan;
    private $sppt;
    private $provinsi;
    private $kabupaten;
    private $year;
    private $kecamatan;
    private $pembayaranSppt;
    private $datObjekPajak;
    private $secondDb;
    private $date;
    private $dateTime;
    private $timestamp;
    private $pbbMinimal;
    private $skpdkb;
    private $datOpBumi;
    private $thirdDb;
    private $datSubjekPajak;
    private $pelayananBphtb;
    private $storage;

    public function __construct(
        User $user,
        Pelayanan $pelayanan,
        Sppt $sppt,
        PbbMinimal $pbbMinimal,
        Kecamatan $kecamatan,
        PembayaranSppt $pembayaranSppt,
        DatObjekPajak $datObjekPajak,
        Skpdkb $skpdkb,
        DatOpBumi $datOpBumi,
        CheckerHelpers $checkerHelpers,
        PendaftaranRepositories $pendaftaranRepositories,
        DatSubjekPajak $datSubjekPajak,
        PelayananBphtb $pelayananBphtb,
    ) {
        /**
         * initialize model
         */
        $this->user = $user;
        $this->pelayanan = $pelayanan;
        $this->sppt = $sppt;
        $this->kecamatan = $kecamatan;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->pbbMinimal = $pbbMinimal;
        $this->skpdkb = $skpdkb;
        $this->datOpBumi = $datOpBumi;
        $this->datSubjekPajak = $datSubjekPajak;
        $this->pelayananBphtb = $pelayananBphtb;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * initialize repositories
         */
        $this->pendaftaranRepositories = $pendaftaranRepositories;

        /**
         * static variable
         */
        $this->provinsi  = 'SUMATERA UTARA';
        $this->kabupaten = 'BINJAI';
        $this->year      = Carbon::now()->format('Y');
        $this->secondDb = DB::connection('second_mysql');
        $this->thirdDb = DB::connection('third_mysql');
        $this->dateTime  = 'Y-m-d H:i:s';
        $this->date      = Carbon::now()->toDateString();
        $this->timestamp = gmdate("Y-m-d\TH:i:s.000\Z");
        $this->storage = path('pelayanan bphtb');
    }

    /**
     * permohonan
     */
    public function permohonan($noPelayanan)
    {
        try {
            /**
             * get data
             */
            $data = $this->pendaftaranRepositories->get($noPelayanan);
            if ($data['status'] == false) :
                throw new \Exception($this->outputMessage('not found', 'pelayanan'));
            endif;

            /**
             * get operator
             */
            $getOperator = $this->checkerHelpers->userChecker(['uuid_user' => $data['data']['created_by']]);
            if (is_null($getOperator)) :
                throw new \Exception($this->outputMessage('not found', 'operator'));
            endif;

            $data = [
                'tanggal_pendaftaran' => $data['data']['tanggal_pendaftaran'],
                'id_pemohon'          => $data['data']['spop']['id_pemohon'],
                'jenis_pelayanan'     => $data['data']['spop']['jenis_pelayanan'],
                'operator'            => $getOperator->name,
                'nama_lengkap'        => $data['data']['spop']['nama_lengkap']
            ];

            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Throwable $e) {
            $response  = $this->error($e->getMessage());
        }

        return $response;
    }

    /**
     * surat keterangan NJOP
     */
    public function suratKeteranganNjop($param)
    {
        try {
            /**
             * get data pelayanan
             */
            $getPelayanan = $this->pelayanan->selectRaw('CONCAT(op_kd_provinsi, op_kd_kabupaten, op_kd_kecamatan, op_kd_kelurahan, op_kd_blok, no_urut, status_kolektif) AS nop')
                ->where(['nomor_pelayanan' => $param])
                ->first();
            $param = is_null($getPelayanan) ? $param : $getPelayanan->nop;

            /**
             * get dat objek pajak
             */
            $getDatObjekPajak = $this->datObjekPajak->select(
                'KD_KECAMATAN AS op_kd_kecamatan',
                'KD_KELURAHAN AS op_kd_kelurahan',
                'KD_BLOK AS op_kd_blok',
                'NO_URUT AS no_urut',
                'KD_JNS_OP AS status_kolektif',
                'TOTAL_LUAS_BNG AS op_luas_bangunan',
                'JALAN_OP AS op_alamat',
                'TOTAL_LUAS_BUMI AS op_luas_tanah',
                'NJOP_BUMI AS njop_bumi',
                'NJOP_BNG AS njop_bangunan',
                'SUBJEK_PAJAK_ID',
                'BLOK_KAV_NO_OP AS no_sertifikat',
            )
                ->selectRaw('(SELECT NM_KECAMATAN FROM ref_kecamatan WHERE ref_kecamatan.KD_KECAMATAN = dat_objek_pajak.KD_KECAMATAN) AS kecamatan')
                ->selectRaw('(SELECT NM_KELURAHAN FROM ref_kelurahan WHERE ref_kelurahan.KD_KECAMATAN = dat_objek_pajak.KD_KECAMATAN AND ref_kelurahan.KD_KELURAHAN = dat_objek_pajak.KD_KELURAHAN) AS kelurahan')
                ->selectRaw('(SELECT JNS_BUMI FROM dat_op_bumi WHERE CONCAT(dat_op_bumi.KD_PROPINSI, dat_op_bumi.KD_DATI2, dat_op_bumi.KD_KECAMATAN, dat_op_bumi.KD_KELURAHAN, dat_op_bumi.KD_BLOK, dat_op_bumi.NO_URUT, dat_op_bumi.KD_JNS_OP) = CONCAT(dat_objek_pajak.KD_PROPINSI, dat_objek_pajak.KD_DATI2, dat_objek_pajak.KD_KECAMATAN, dat_objek_pajak.KD_KELURAHAN, dat_objek_pajak.KD_BLOK, dat_objek_pajak.NO_URUT, dat_objek_pajak.KD_JNS_OP)) AS jns_bumi')
                ->selectRaw('IF(TOTAL_LUAS_BNG = "0" OR TOTAL_LUAS_BNG IS NULL, (SELECT nm_jpb FROM ref_jpb_tanah WHERE kd_jpb = jns_bumi),(SELECT nm_jpb FROM ref_jpb WHERE kd_jpb = jns_bumi)) AS jenis_penggunaan')
                ->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $param . '"')->first();
            if (is_null($getDatObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'Data Objek Pajak'));
            endif;

            /**
             * get subjek pajak
             */
            $getSubjekPajak = $this->datSubjekPajak->select(
                'NM_WP AS sp_nama_lengkap',
                'JALAN_WP AS sp_alamat',
                'RW_WP AS rw',
                'RT_WP AS rt',
                'KELURAHAN_WP',
                'KOTA_WP'
            )
                ->where('SUBJEK_PAJAK_ID', $getDatObjekPajak->SUBJEK_PAJAK_ID)->first();
            if (is_null($getSubjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'Data Subjek Pajak'));
            endif;

            $nop = $this->nop($getDatObjekPajak->op_kd_kecamatan, $getDatObjekPajak->op_kd_kelurahan, $getDatObjekPajak->op_kd_blok, $getDatObjekPajak->no_urut, $getDatObjekPajak->status_kolektif);
            $jenisOp = is_null($getDatObjekPajak->op_luas_bangunan) || $getDatObjekPajak->op_luas_bangunan == 0 ? "Tanah Kosong" : "Tanah dan Bangunan";
            $jenisPenggunaan = $getDatObjekPajak->jns_bumi == "4" ? 'fasilitas umum' : $getDatObjekPajak->jenis_penggunaan;

            $data = [
                'nop'                   => $nop,
                'jenis_op'              => $jenisOp,
                'jenis_penggunaan'      => strtoupper($jenisPenggunaan),
                'letak_op'              => $getDatObjekPajak->op_alamat,
                'luas_bumi'             => $getDatObjekPajak->op_luas_tanah,
                'luas_bangunan'         => $getDatObjekPajak->op_luas_bangunan,
                'njop_bumi'             => $getDatObjekPajak->njop_bumi,
                'njop_bumi_bersama'     => "",
                'njop_bangunan'         => $getDatObjekPajak->njop_bangunan,
                'njop_bangunan_bersama' => "",
                'nama_wp'               => $getSubjekPajak->sp_nama_lengkap,
                'alamat_wp'             => $getSubjekPajak->sp_alamat,
                'rt'                    => $getSubjekPajak->rt,
                'rw'                    => $getSubjekPajak->rw,
                'kecamatan'             => $getDatObjekPajak->kecamatan,
                'kelurahan'             => $getDatObjekPajak->kelurahan,
                'no_sertifikat'         => $getDatObjekPajak->no_sertifikat,
                'kelurahan_wp'          => $getSubjekPajak->KELURAHAN_WP,
                'kota_wp'               => $getSubjekPajak->KOTA_WP,
            ];

            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Throwable $e) {
            $response  = $this->error($e->getMessage());
        }

        return $response;
    }

    /**
     * SPPT
     */
    public function sppt($spptRequest)
    {
        try {
            $kdKecamatan = $spptRequest['KD_KECAMATAN'];
            $kdKelurahan = $spptRequest['KD_KELURAHAN'];
            $kdBlok = $spptRequest['KD_BLOK'];
            $noUrut = $spptRequest['NO_URUT'];
            $statusKolektif = $spptRequest['status_kolektif'];
            $tahun = $spptRequest['tahun'];

            $where = [
                'KD_KECAMATAN' => $kdKecamatan,
                'KD_KELURAHAN' => $kdKelurahan,
                'KD_BLOK' => $kdBlok,
                'NO_URUT' => $noUrut,
                'KD_JNS_OP' => $statusKolektif
            ];

            /**
             * get NOP
             */
            $nop  =  $this->nop($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif);

            /**
             * check jenis bumi
             */
            $checkJenisbumi = $this->datOpBumi->select('JNS_BUMI')->where(array_merge([
                'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
                'KD_DATI2' => globalAttribute()['kdKota'],
            ], $where))
                ->first();
            if (is_null($checkJenisbumi) || $checkJenisbumi->JNS_BUMI == "4") :
                throw new \Exception($this->outputMessage('fasum', $nop));
            endif;

            /**
             * get data from sppt
             */
            $getDataSppt = $this->sppt->select(
                'JLN_WP_SPPT',
                'NM_WP_SPPT',
                'RW_WP_SPPT',
                'RT_WP_SPPT',
                'KELURAHAN_WP_SPPT',
                'THN_PAJAK_SPPT',
                'LUAS_BUMI_SPPT',
                'LUAS_BNG_SPPT',
                'KOTA_WP_SPPT',
                'KD_KLS_TANAH',
                'KD_KLS_BNG',
                'NJOP_BUMI_SPPT',
                'NJOP_BNG_SPPT',
                'NJOPTKP_SPPT',
                'PBB_YG_HARUS_DIBAYAR_SPPT'
            )
                ->where(array_merge([
                    'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
                    'KD_DATI2' => globalAttribute()['kdKota'],
                    'THN_PAJAK_SPPT' => $tahun,
                ], $where))
                ->orderByDesc('THN_PAJAK_SPPT')
                ->first();
            if (is_null($getDataSppt)) :
                throw new \Exception($this->outputMessage('not found', 'sppt'));
            endif;

            /**
             * get data objek pajak
             */
            $getDatObjekPajak = $this->checkerHelpers->datObjekPajakChecker($where);
            if (is_null($getDatObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'objek pajak'));
            endif;

            /**
             * get regional
             */
            $regionSp = $this->kecamatan->join("kabupaten", "kecamatan.id_kabupaten", "=", "kabupaten.id_kabupaten")
                ->join("provinsi", "kabupaten.id_provinsi", "=", "provinsi.id_provinsi")
                ->join("kelurahan", "kecamatan.id_kecamatan", "=", "kelurahan.id_kecamatan")
                ->where('kabupaten.nama_kabupaten', 'like', "%$getDataSppt->KOTA_WP_SPPT%")
                ->where('nama_kelurahan', $getDataSppt->KELURAHAN_WP_SPPT)
                ->first();

            if (is_null($regionSp)) :
                $kecamatanWp = null;
                $provinsiWp = null;
            else :
                $kecamatanWp = $regionSp->nama_kecamatan;
                $provinsiWp = $regionSp->nama_provinsi;;
            endif;

            $nilaiTanah = $getDataSppt->NJOP_BUMI_SPPT == 0 ? 0 : $getDataSppt->NJOP_BUMI_SPPT / $getDataSppt->LUAS_BUMI_SPPT;
            $nilaiBangunan = $getDataSppt->NJOP_BNG_SPPT == 0 ? 0 : $getDataSppt->NJOP_BNG_SPPT / $getDataSppt->LUAS_BNG_SPPT;
            $kecamatanOp = $this->checkerHelpers->refrensiKecamatanChecker(['KD_KECAMATAN' => $kdKecamatan])->NM_KECAMATAN;
            $kelurahanOp = $this->checkerHelpers->refrensiKelurahanChecker(['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan])->NM_KELURAHAN;
            $njopBumiBangunan = $getDataSppt->NJOP_BUMI_SPPT + $getDataSppt->NJOP_BNG_SPPT;
            $faktorPengali = $this->faktorPengali($getDataSppt->NJOP_BUMI_SPPT, $getDataSppt->NJOP_BNG_SPPT);
            $bumiBangunanNjoptkp = $njopBumiBangunan - $getDataSppt->NJOPTKP_SPPT;
            $nilaiAkhir = $faktorPengali * ($bumiBangunanNjoptkp);

            /**
             * get jatuh tempo
             */
            $getJatuhTempo = $this->pbbMinimal->select('TGL_JATUH_TEMPO')->orderBy('THN_PBB_MINIMAL', 'desc')->first();
            $getJatuhTempo = Carbon::parse($getJatuhTempo->TGL_JATUH_TEMPO)->locale('id');
            $getJatuhTempo->settings(['formatFunction' => 'translatedFormat']);

            $data = [
                'nop'   => $nop,
                'nama_sp' => $getDataSppt->NM_WP_SPPT,
                'tahun' => $getDataSppt->THN_PAJAK_SPPT,
                'alamat_sp' => $getDataSppt->JLN_WP_SPPT,
                'rt_sp' => $getDataSppt->RT_WP_SPPT,
                'rw_sp' => $getDataSppt->RW_WP_SPPT,
                'kecamatan_sp' => $kecamatanWp,
                'kelurahan_sp' => $getDataSppt->KELURAHAN_WP_SPPT,
                'kota_sp' => $getDataSppt->KOTA_WP_SPPT,
                'provinsi_sp' => $provinsiWp,
                'alamat_op' => $getDatObjekPajak->JALAN_OP,
                'rt_op' => $getDatObjekPajak->RT_OP,
                'rw_op' => $getDatObjekPajak->RW_OP,
                'kecamatan_op' => $kecamatanOp,
                'kelurahan_op' => $kelurahanOp,
                'kota_op' => $this->kabupaten,
                'provinsi_op' => $this->provinsi,
                'kd_kelas_tanah' => $getDataSppt->KD_KLS_TANAH,
                'kd_kelas_bangunan' => $getDataSppt->KD_KLS_BNG,
                'nilai_kelas_tanah' => $nilaiTanah,
                'nilai_kelas_bangunan' => $nilaiBangunan,
                'luas_bumi' => $getDataSppt->LUAS_BUMI_SPPT,
                'luas_bangunan' => $getDataSppt->LUAS_BNG_SPPT,
                'njop_bumi' => $getDataSppt->NJOP_BUMI_SPPT,
                'njop_bangunan' => $getDataSppt->NJOP_BNG_SPPT,
                'njop_bumi_bangunan' => $njopBumiBangunan,
                'njoptkp_sppt' => $getDataSppt->NJOPTKP_SPPT,
                'faktor_pengali' => $faktorPengali * 100,
                'bumi_bangunan_njoptkp' => $bumiBangunanNjoptkp,
                'nilai_akhir' => $nilaiAkhir,
                'jatuh_tempo' => $getJatuhTempo->format('j F Y'),
                'terbilang' => ucwords($this->konversiKeKalimat($nilaiAkhir)),
                'pbb_yg_harus_dibayar_sppt' => $getDataSppt->PBB_YG_HARUS_DIBAYAR_SPPT
            ];

            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Throwable $e) {
            $response  = $this->error($e->getMessage());
        }

        return $response;
    }

    /**
     * SPPT masal
     */
    public function spptMasal($kdKecamatan, $kdKelurahan, $kdBlok, $tahun, $key)
    {
        try {

            /**
             * get data from sppt
             */
            $masalOrBuku45 = $key == 'buku 45' ? '(SELECT(NJOP_BUMI_SPPT + NJOP_BNG_SPPT)) >= 1000000000' : '(SELECT(NJOP_BUMI_SPPT + NJOP_BNG_SPPT)) < 1000000000';
            $getDataSppt = $this->sppt->select(
                'JLN_WP_SPPT',
                'NM_WP_SPPT',
                'RW_WP_SPPT',
                'RT_WP_SPPT',
                'KELURAHAN_WP_SPPT',
                'THN_PAJAK_SPPT',
                'LUAS_BUMI_SPPT',
                'LUAS_BNG_SPPT',
                'KOTA_WP_SPPT',
                'NO_URUT',
                'KD_JNS_OP',
                'KD_KLS_TANAH',
                'KD_KLS_BNG',
                'NJOP_BUMI_SPPT',
                'NJOP_BNG_SPPT',
                'NJOPTKP_SPPT',
                'PBB_YG_HARUS_DIBAYAR_SPPT'
            )
                ->where([
                    'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
                    'KD_DATI2' => globalAttribute()['kdKota'],
                    'KD_KECAMATAN' => $kdKecamatan,
                    'KD_KELURAHAN' => $kdKelurahan,
                    'KD_BLOK' => $kdBlok,
                    'THN_PAJAK_SPPT' => $tahun
                ])
                ->whereRaw($masalOrBuku45)
                ->orderByDesc('THN_PAJAK_SPPT')
                ->get();

            $output = [];
            foreach ($getDataSppt as $key => $value) :
                $noUrut = $value->NO_URUT;
                $statusKolektif = $value->KD_JNS_OP;

                $where = [
                    'KD_KECAMATAN' => $kdKecamatan,
                    'KD_KELURAHAN' => $kdKelurahan,
                    'KD_BLOK' => $kdBlok,
                    'NO_URUT' => $noUrut,
                    'KD_JNS_OP' => $statusKolektif,
                ];

                /**
                 * get NOP
                 */
                $nop  =  $this->nop($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif);

                /**
                 * check jenis bumi
                 */
                $checkJenisbumi = $this->datOpBumi->select('JNS_BUMI')->where(array_merge([
                    'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
                    'KD_DATI2' => globalAttribute()['kdKota'],
                ], $where))->first();
                if (!is_null($checkJenisbumi)) :
                    if ($checkJenisbumi->JNS_BUMI != "4") :
                        /**
                         * get data objek pajak
                         */
                        $getDatObjekPajak = $this->checkerHelpers->datObjekPajakChecker($where);
                        if (is_null($getDatObjekPajak)) :
                            throw new \Exception($this->outputMessage('not found', 'objek pajak nop ' . $nop));
                        endif;

                        /**
                         * get regional
                         */
                        $regionSp = $this->kecamatan->join("kabupaten", "kecamatan.id_kabupaten", "=", "kabupaten.id_kabupaten")
                            ->join("provinsi", "kabupaten.id_provinsi", "=", "provinsi.id_provinsi")
                            ->join("kelurahan", "kecamatan.id_kecamatan", "=", "kelurahan.id_kecamatan")
                            ->where('kabupaten.nama_kabupaten', 'like', "%$value->KOTA_WP_SPPT%")
                            ->where('nama_kelurahan', $value->KELURAHAN_WP_SPPT)
                            ->first();

                        if (is_null($regionSp)) :
                            $kecamatanWp = null;
                            $provinsiWp = null;
                        else :
                            $kecamatanWp = $regionSp->nama_kecamatan;
                            $provinsiWp = $regionSp->nama_provinsi;;
                        endif;

                        $nilaiTanah = $value->NJOP_BUMI_SPPT == 0 ? 0 : $value->NJOP_BUMI_SPPT / $value->LUAS_BUMI_SPPT;
                        $nilaiBangunan = $value->NJOP_BNG_SPPT == 0 ? 0 : $value->NJOP_BNG_SPPT / $value->LUAS_BNG_SPPT;
                        $kecamatanOp = $this->checkerHelpers->refrensiKecamatanChecker(['KD_KECAMATAN' => $kdKecamatan])->NM_KECAMATAN;
                        $kelurahanOp = $this->checkerHelpers->refrensiKelurahanChecker(['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan])->NM_KELURAHAN;
                        $njopBumiBangunan = $value->NJOP_BUMI_SPPT + $value->NJOP_BNG_SPPT;
                        $faktorPengali = $this->faktorPengali($value->NJOP_BUMI_SPPT, $value->NJOP_BNG_SPPT);
                        $bumiBangunanNjoptkp = $njopBumiBangunan - $value->NJOPTKP_SPPT;
                        $nilaiAkhir = $faktorPengali * ($bumiBangunanNjoptkp);

                        /**
                         * get jatuh tempo
                         */
                        $getJatuhTempo = $this->pbbMinimal->select('TGL_JATUH_TEMPO')->orderBy('THN_PBB_MINIMAL', 'desc')->first();
                        $getJatuhTempo = Carbon::parse($getJatuhTempo->TGL_JATUH_TEMPO)->locale('id');
                        $getJatuhTempo->settings(['formatFunction' => 'translatedFormat']);

                        $set = [
                            'nop'   => $nop,
                            'nama_sp' => $value->NM_WP_SPPT,
                            'tahun' => $value->THN_PAJAK_SPPT,
                            'alamat_sp' => $value->JLN_WP_SPPT,
                            'rt_sp' => $value->RT_WP_SPPT,
                            'rw_sp' => $value->RW_WP_SPPT,
                            'kecamatan_sp' => $kecamatanWp,
                            'kelurahan_sp' => $value->KELURAHAN_WP_SPPT,
                            'kota_sp' => $value->KOTA_WP_SPPT,
                            'provinsi_sp' => $provinsiWp,
                            'alamat_op' => $getDatObjekPajak->JALAN_OP,
                            'rt_op' => $getDatObjekPajak->RT_OP,
                            'rw_op' => $getDatObjekPajak->RW_OP,
                            'kecamatan_op' => $kecamatanOp,
                            'kelurahan_op' => $kelurahanOp,
                            'kota_op' => $this->kabupaten,
                            'provinsi_op' => $this->provinsi,
                            'kd_kelas_tanah' => $value->KD_KLS_TANAH,
                            'kd_kelas_bangunan' => $value->KD_KLS_BNG,
                            'nilai_kelas_tanah' => $nilaiTanah,
                            'nilai_kelas_bangunan' => $nilaiBangunan,
                            'luas_bumi' => $value->LUAS_BUMI_SPPT,
                            'luas_bangunan' => $value->LUAS_BNG_SPPT,
                            'njop_bumi' => $value->NJOP_BUMI_SPPT,
                            'njop_bangunan' => $value->NJOP_BNG_SPPT,
                            'njop_bumi_bangunan' => $njopBumiBangunan,
                            'njoptkp_sppt' => $value->NJOPTKP_SPPT,
                            'faktor_pengali' => $faktorPengali * 100,
                            'bumi_bangunan_njoptkp' => $bumiBangunanNjoptkp,
                            'nilai_akhir' => $nilaiAkhir,
                            'jatuh_tempo' => $getJatuhTempo->format('j F Y'),
                            'terbilang' => ucwords($this->konversiKeKalimat($nilaiAkhir)),
                            'pbb_yg_harus_dibayar_sppt' => $value->PBB_YG_HARUS_DIBAYAR_SPPT
                        ];
                        array_push($output, $set);
                    endif;
                endif;

            endforeach;

            $response  = $this->successData($this->outputMessage('data', count($output)), $output);
        } catch (\Throwable $e) {
            $response  = $this->error($e->getMessage());
        }

        return $response;
    }

    /**
     * stts
     */
    public function stts($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif, $tahun)
    {
        try {
            $where = [
                'KD_PROPINSI'    => globalAttribute()['kdProvinsi'],
                'KD_DATI2'       => globalAttribute()['kdKota'],
                'KD_KECAMATAN'   => $kdKecamatan,
                'KD_KELURAHAN'   => $kdKelurahan,
                'KD_BLOK'        => $kdBlok,
                'NO_URUT'        => $noUrut,
                'KD_JNS_OP'      => $statusKolektif
            ];

            /**
             * get data from pembayaran sppt
             */
            $getPembayaranSppt = $this->pembayaranSppt->select(
                'TGL_PEMBAYARAN_SPPT',
                'DENDA_SPPT',
                'KODE_CABANG',
                'KODE_PENGESAHAN'
            )->where(array_merge($where, ['THN_PAJAK_SPPT' => $tahun]))->first();
            if (is_null($getPembayaranSppt)) :
                throw new \Exception($this->outputMessage('not found', 'pembayaran sppt'));
            endif;
            $data['detail_pembayaran'] = [
                'tanggal_bayar' => $getPembayaranSppt->TGL_PEMBAYARAN_SPPT,
                'lokasi_transaksi' => $getPembayaranSppt->KODE_CABANG,
                'rekening_tujuan' => 'RKUD PEMKO BINJAI',
                'no_arsip_bank' => $getPembayaranSppt->KODE_PENGESAHAN,
                'kode_user' => substr($getPembayaranSppt->KODE_PENGESAHAN, 0, 5)
            ];

            /**
             * get data from sppt
             */
            $getDataSppt = $this->sppt->select(
                'JLN_WP_SPPT',
                'NM_WP_SPPT',
                'PBB_YG_HARUS_DIBAYAR_SPPT',
                'THN_PAJAK_SPPT'
            )
                ->where(array_merge($where, ['THN_PAJAK_SPPT' => $tahun]))
                ->first();
            if (is_null($getDataSppt)) :
                throw new \Exception($this->outputMessage('not found', 'sppt'));
            endif;

            /**
             * get data from data objek pajak
             */
            $getDataObjekPajak = $this->datObjekPajak->select('JALAN_OP')
                ->where($where)
                ->first();
            if (is_null($getDataSppt)) :
                throw new \Exception($this->outputMessage('not found', ' data objek pajak'));
            endif;

            $nop = $this->nop($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif);
            $total = $getPembayaranSppt->DENDA_SPPT + $getDataSppt->PBB_YG_HARUS_DIBAYAR_SPPT;
            $data['rincian_setoran'] = [
                'no_sts' => $nop,
                'nop' => $nop,
                'nama_wp' => $getDataSppt->NM_WP_SPPT,
                'alamat_wp' => $getDataSppt->JLN_WP_SPPT,
                'alamat_op' => $getDataObjekPajak->JALAN_OP,
                'masa_pajak' => $getDataSppt->THN_PAJAK_SPPT,
                'nominal' => $getDataSppt->PBB_YG_HARUS_DIBAYAR_SPPT,
                'denda' => $getPembayaranSppt->DENDA_SPPT,
                'total' => $total,
                'terbilang' => ucwords($this->konversiKeKalimat($total)),
            ];

            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Throwable $e) {
            $response  = $this->error($e->getMessage());
        }

        return $response;
    }

    /**
     * SPPT masal
     */
    public function spptMasalMultiple($spptRequest)
    {
        try {

            $kdKecamatan = $spptRequest['KD_KECAMATAN'];
            $kdKelurahan = $spptRequest['KD_KELURAHAN'];
            $kdBlok = $spptRequest['KD_BLOK'];
            $noUrutAwal = $spptRequest['no_urut_awal'];
            $noUrutAkhir = $spptRequest['no_urut_akhir'];
            $statusKolektif = $spptRequest['status_kolektif'];

            /**
             * get data from sppt
             */

            $output = [];
            foreach ($kdBlok as $keys => $values) :
                $where = [
                    'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
                    'KD_DATI2' => globalAttribute()['kdKota'],
                    'KD_KECAMATAN' => $kdKecamatan,
                    'KD_KELURAHAN' => $kdKelurahan,
                    'KD_BLOK' => $values,
                    'KD_JNS_OP' => $statusKolektif[$keys]
                ];
                $getDataSppt = $this->sppt->select(
                    'JLN_WP_SPPT',
                    'NM_WP_SPPT',
                    'RW_WP_SPPT',
                    'RT_WP_SPPT',
                    'KELURAHAN_WP_SPPT',
                    'THN_PAJAK_SPPT',
                    'LUAS_BUMI_SPPT',
                    'LUAS_BNG_SPPT',
                    'KOTA_WP_SPPT',
                    'NO_URUT',
                    'KD_JNS_OP',
                    'KD_KLS_TANAH',
                    'KD_KLS_BNG',
                    'NJOP_BUMI_SPPT',
                    'NJOP_BNG_SPPT',
                    'NJOPTKP_SPPT',
                )
                    ->where($where)
                    ->whereBetween('NO_URUT', [$noUrutAwal[$keys], $noUrutAkhir[$keys]])
                    ->orderByDesc('THN_PAJAK_SPPT')
                    ->get();

                foreach ($getDataSppt as $key => $value) :
                    /**
                     * check jenis bumi
                     */
                    $checkJenisbumi = $this->datOpBumi->select('JNS_BUMI')->where($where)->first();
                    if (!is_null($checkJenisbumi)) :
                        if ($checkJenisbumi->JNS_BUMI != "4") :

                            $noUrut = $value->NO_URUT;
                            $kdJnsOp = $value->KD_JNS_OP;

                            $where = [
                                'KD_KECAMATAN' => $kdKecamatan,
                                'KD_KELURAHAN' => $kdKelurahan,
                                'KD_BLOK' => $values,
                                'NO_URUT' => $noUrut,
                                'KD_JNS_OP' => $kdJnsOp,
                            ];

                            /**
                             * get NOP
                             */
                            $nop  =  $this->nop($kdKecamatan, $kdKelurahan, $values, $noUrut, $kdJnsOp);

                            /**
                             * get data objek pajak
                             */
                            $getDatObjekPajak = $this->checkerHelpers->datObjekPajakChecker($where);
                            if (is_null($getDatObjekPajak)) :
                                throw new \Exception($this->outputMessage('not found', 'objek pajak'));
                            endif;

                            /**
                             * get regional
                             */
                            $regionSp = $this->kecamatan->join("kabupaten", "kecamatan.id_kabupaten", "=", "kabupaten.id_kabupaten")
                                ->join("provinsi", "kabupaten.id_provinsi", "=", "provinsi.id_provinsi")
                                ->join("kelurahan", "kecamatan.id_kecamatan", "=", "kelurahan.id_kecamatan")
                                ->where('kabupaten.nama_kabupaten', 'like', "%$value->KOTA_WP_SPPT%")
                                ->where('nama_kelurahan', $value->KELURAHAN_WP_SPPT)
                                ->first();

                            if (is_null($regionSp)) :
                                $kecamatanWp = null;
                                $provinsiWp = null;
                            else :
                                $kecamatanWp = $regionSp->nama_kecamatan;
                                $provinsiWp = $regionSp->nama_provinsi;;
                            endif;

                            $nilaiTanah = $this->checkerHelpers->kelasBumiChecker(['KD_KLS_TANAH' => $value->KD_KLS_TANAH]);
                            $nilaiBangunan = $this->checkerHelpers->kelasBangunanChecker(['KD_KLS_BNG' => $value->KD_KLS_BNG]);
                            $kecamatanOp = $this->checkerHelpers->refrensiKecamatanChecker(['KD_KECAMATAN' => $kdKecamatan])->NM_KECAMATAN;
                            $kelurahanOp = $this->checkerHelpers->refrensiKelurahanChecker(['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan])->NM_KELURAHAN;
                            $njopBumiBangunan = $value->NJOP_BUMI_SPPT + $value->NJOP_BNG_SPPT;
                            $faktorPengali = $this->faktorPengali($value->NJOP_BUMI_SPPT, $value->NJOP_BNG_SPPT);
                            $bumiBangunanNjoptkp = $njopBumiBangunan - $value->NJOPTKP_SPPT;
                            $nilaiAkhir = $faktorPengali * ($bumiBangunanNjoptkp);

                            /**
                             * get jatuh tempo
                             */
                            $getJatuhTempo = $this->pbbMinimal->select('TGL_JATUH_TEMPO')->orderBy('THN_PBB_MINIMAL', 'desc')->first();
                            $getJatuhTempo = Carbon::parse($getJatuhTempo->TGL_JATUH_TEMPO)->locale('id');
                            $getJatuhTempo->settings(['formatFunction' => 'translatedFormat']);

                            $set = [
                                'nop'   => $nop,
                                'nama_sp' => $value->NM_WP_SPPT,
                                'tahun' => $value->THN_PAJAK_SPPT,
                                'alamat_sp' => $value->JLN_WP_SPPT,
                                'rt_sp' => $value->RT_WP_SPPT,
                                'rw_sp' => $value->RW_WP_SPPT,
                                'kecamatan_sp' => $kecamatanWp,
                                'kelurahan_sp' => $value->KELURAHAN_WP_SPPT,
                                'kota_sp' => $value->KOTA_WP_SPPT,
                                'provinsi_sp' => $provinsiWp,
                                'alamat_op' => $getDatObjekPajak->JALAN_OP,
                                'rt_op' => $getDatObjekPajak->RT_OP,
                                'rw_op' => $getDatObjekPajak->RW_OP,
                                'kecamatan_op' => $kecamatanOp,
                                'kelurahan_op' => $kelurahanOp,
                                'kota_op' => $this->kabupaten,
                                'provinsi_op' => $this->provinsi,
                                'kd_kelas_tanah' => $value->KD_KLS_TANAH,
                                'kd_kelas_bangunan' => $value->KD_KLS_BNG,
                                'nilai_kelas_tanah' => $nilaiTanah->NILAI_PER_M2_TANAH * 1000,
                                'nilai_kelas_bangunan' => $nilaiBangunan->NILAI_PER_M2_BNG * 1000,
                                'luas_bumi' => $value->LUAS_BUMI_SPPT,
                                'luas_bangunan' => $value->LUAS_BNG_SPPT,
                                'njop_bumi' => $value->NJOP_BUMI_SPPT,
                                'njop_bangunan' => $value->NJOP_BNG_SPPT,
                                'njop_bumi_bangunan' => $njopBumiBangunan,
                                'njoptkp_sppt' => $value->NJOPTKP_SPPT,
                                'faktor_pengali' => $faktorPengali * 100,
                                'bumi_bangunan_njoptkp' => $bumiBangunanNjoptkp,
                                'nilai_akhir' => $nilaiAkhir,
                                'jatuh_tempo' => $getJatuhTempo->format('j F Y'),
                                'terbilang' => ucwords($this->konversiKeKalimat($nilaiAkhir)),
                            ];
                            array_push($output, $set);
                        endif;
                    endif;
                endforeach;
            endforeach;

            $response  = $this->successData($this->outputMessage('data', count($output)), $output);
        } catch (\Throwable $e) {
            $response  = $this->error($e->getMessage());
        }

        return $response;
    }

    /**
     * sspd
     */
    public function sspd($uuidPelayananBphtb)
    {
        try {

            /**
             * detail bphtb
             */
            $getDetailBphtb = $this->queryDetail($uuidPelayananBphtb);
            $nop = $getDetailBphtb['nop'];
            $id = $getDetailBphtb['id'];
            $noSts = $getDetailBphtb['no_sts'];

            /**
             * get sts history
             */
            $getSts = $this->secondDb->table('STS_History')->select('No_STS')->where(['No_Pokok_WP' => $nop, 'No_STS' => $noSts])->first();
            $noSts = is_null($getSts) ? 0 : $getSts->No_STS;

            /**
             * data A
             */
            $dataA = [
                'nama'          => $getDetailBphtb['a']['nama_wp_2'],
                'nik'           => $getDetailBphtb['a']['nik'],
                'alamat'        => $getDetailBphtb['a']['alamat_wp_2'],
                'kelurahan'     => $getDetailBphtb['a']['kelurahan']['nama'],
                'kabupaten'     => $getDetailBphtb['a']['kabupaten']['nama'],
                'rt'            => '-',
                'kecamatan'     => $getDetailBphtb['a']['kecamatan']['nama'],
                'kode_pos'      => $getDetailBphtb['a']['kode_pos'],
                'no_registrasi' => $getDetailBphtb['a']['no_registrasi'],
                'tgl_dibuat'    => $getDetailBphtb['a']['tgl_dibuat'],
                'tgl_bayar'     => $getDetailBphtb['a']['tgl_bayar'],
                'status_verifikasi' => $getDetailBphtb['d']['status_verifikasi'],
                'notaris'           => $getDetailBphtb['a']['notaris'],
            ];

            /**
             * data B
             */
            $dataB = array_merge($getDetailBphtb['b'], [
                'kecamatan'          => $getDetailBphtb['a']['dph_kecamatan']['nama'],
                'kelurahan'          => $getDetailBphtb['a']['dph_kelurahan']['nama'],
                'no_sertifikat'      => $getDetailBphtb['a']['no_sertifikat'],
                'jenis_perolehan'    => $getDetailBphtb['a']['jenis_perolehan'],
                'id_jenis_perolehan' => $getDetailBphtb['a']['id_jenis_perolehan']
            ]);

            /**
             * briva code
             */
            $brivaNo = 0;
            // $tglDibuat = date('Y-m-d', strtotime($getDetailBphtb['a']['tgl_dibuat']));
            // $tglStop   = '2024-04-29';
            // if (($getDetailBphtb['d']['status_verifikasi']['value'] >= 2 && $getDetailBphtb['a']['status_bayar'] == 'sudah') ||
            //     ($getDetailBphtb['d']['status_verifikasi']['value'] >= 2 && $getDetailBphtb['a']['status_bayar'] == 'belum' && $tglDibuat < $tglStop)
            // ) :
            //     // $brivaGenerateToken = $this->brivaGenerateToken();
            //     // $expired = date($this->dateTime, strtotime('+60 days', strtotime($this->date)));
            //     $custCode = $this->year . $this->nomorUrutBrivaBphtb($nop);
            //     // $payload = [
            //     //     'custCode'        => $custCode, // 4 digit tahun pajak + 9 digit no urut
            //     //     'nama'            => $getDetailBphtb['a']['nama_wp_2'], // nama wp
            //     //     'amount'          => $getDetailBphtb['jumlah_disetor'], // jumlah tagihan yg dibayar
            //     //     'keterangan'      => "BPHTB", // jenis pajak
            //     //     'expiredDate'     => $expired
            //     // ];
            //     // $signature = $this->brivaGenareteSignature($brivaGenerateToken->access_token, $payload, $this->timestamp);
            //     // $createBriva = $this->createBriva($brivaGenerateToken->access_token, $this->timestamp, $signature, $payload);

            //     /**
            //      * formatting briva
            //      */
            //     $brivaNo = $this->kodeBriva($custCode);
            //     $part1 = substr($brivaNo, 0, 9);   // 140922024
            //     $part2 = substr($brivaNo, 9, 4);    // 0000
            //     $part3 = substr($brivaNo, 13, 5);   // 00041
            //     // Combining the parts with periods
            //     $brivaNo = $part1 . '.' . $part2 . '.' . $part3;
            // else :
            //     $brivaNo = 0;
            // endif;

            /**
             * formatting bank sumut
             */
            $bankSumut = $noSts;
            // Extracting parts of the number
            $part1 = substr($bankSumut, 0, 4);    // 1259
            $part2 = substr($bankSumut, 4, 6);    // 410115
            $part3 = substr($bankSumut, 10, 4);   // 2024
            $part4 = substr($bankSumut, 14, 7);   // 0041064
            // Combining the parts with periods
            $bankSumut = $part1 . '.' . $part2 . '.' . $part3 . '.' . $part4;

            /**
             * get kabid
             */
            $getKabid = $this->checkerHelpers->userChecker(['role' => 'kabid']);
            if (is_null($getKabid)) :
                throw new \Exception($this->outputMessage('not found', 'kabid'));
            endif;

            /**
             * get status bayar dan ntpd
             */
            $ntpd = $this->ntpd($nop, $id, $noSts);

            $data = [
                'a' => $dataA,
                'b' => $dataB,
                'c' => $getDetailBphtb['c'],
                'jumlah_disetor' => $getDetailBphtb['jumlah_disetor'],
                'terbilang' => $getDetailBphtb['terbilang'],
                'kaban' => $getDetailBphtb['kaban'],
                'kode_pembayaran' => $bankSumut,
                'briva' => $brivaNo,
                'kabid' => $getKabid->name,
                'ntpd' => $ntpd['ntpd']
            ];
            $response = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * skpdkb
     */
    public function skpdkb($sspd)
    {
        try {

            /**
             * get skpdkb
             */
            $getSkpdkb = $this->skpdkb->select('npop', 'nilai_pajak')->where(['sspd' => $sspd])->first();
            if (is_null($getSkpdkb)) :
                throw new \Exception($this->outputMessage('not found', ' SKPDKB'));
            endif;

            /**
             * detail bphtb
             */
            $getDetailBphtb = $this->queryDetail($sspd);

            /**
             * data A
             */
            $dataA = [
                'nama'               => $getDetailBphtb['a']['nama_wp_2'],
                'nik'                => $getDetailBphtb['a']['nik'],
                'alamat'             => $getDetailBphtb['a']['alamat_wp_2'],
                'kelurahan'          => $getDetailBphtb['a']['kelurahan']['nama'],
                'kabupaten'          => $getDetailBphtb['a']['kabupaten']['nama'],
                'rt'                 => '-',
                'kecamatan'          => $getDetailBphtb['a']['kecamatan']['nama'],
                'no_registrasi'      => $getDetailBphtb['a']['no_registrasi'],
                'tgl_dibuat'         => $getDetailBphtb['a']['tgl_dibuat'],
                'tgl_bayar'          => $getDetailBphtb['a']['tgl_bayar'],
                'status_verifikasi'  => $getDetailBphtb['d']['status_verifikasi'],
                'npwp'               => $getDetailBphtb['a']['npwp'],
                'kode_pos'           => $getDetailBphtb['a']['kode_pos'],
            ];

            /**
             * data B
             */
            $dataB = array_merge($getDetailBphtb['b'], [
                'nilai_transaksi'    => $getDetailBphtb['b']['nilai_transaksi'],
                'no_sertifikat'      => $getDetailBphtb['a']['no_sertifikat'],
                'jenis_perolehan'    => $getDetailBphtb['a']['jenis_perolehan'],
                'id_jenis_perolehan' => $getDetailBphtb['a']['id_jenis_perolehan']
            ]);

            /**
             * data c
             */
            $dataC = array_merge($getDetailBphtb['c'], [
                'pengurangan_bphtb' => 0,
                'total_bphtb_terhutang' => $getDetailBphtb['c']['bphtb_terhutang'] - 0
            ]);

            /**
             * data d
             */
            $npopkp = $getSkpdkb->npop - $getDetailBphtb['c']['npoptkp'];
            $npopkp = $npopkp < 0 ? 0 : $npopkp;
            $dataD = [
                'npop' => $getSkpdkb->npop,
                'npoptkp' => $getDetailBphtb['c']['npoptkp'],
                'npopkp' => $npopkp,
                'bphtb_terhutang' => $this->bphtbTerhutang($npopkp)
            ];

            $formatter = new NumberFormatter('id_ID', NumberFormatter::SPELLOUT);
            $jumlahDisetor = ($dataD['bphtb_terhutang'] - $dataC['bphtb_terhutang']) < 0 ? 0 : $dataD['bphtb_terhutang'] - $dataC['bphtb_terhutang'];
            $terbilang = $formatter->format($jumlahDisetor) == 'kosong' ? 'nihil' : $formatter->format($jumlahDisetor) . 'rupiah';
            $data = [
                'a' => $dataA,
                'b' => $dataB,
                'c' => $dataC,
                'd' => $dataD,
                'jumlah_disetor' => $jumlahDisetor,
                'terbilang' => ucwords($terbilang)
            ];
            $response = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response = $this->error($e->getMessage());
        }
        return $response;
    }
}

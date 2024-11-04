<?php

namespace App\Repositories\Pelayanan\Bphtb;

/**
 * import component
 */

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * import traits
 */

use App\Traits\Calculation;
use App\Traits\Message;
use App\Traits\Notification;
use App\Traits\Response;
use App\Traits\Converter;
use App\Traits\Generator;
use App\Traits\Bphtb;

/**
 * import models
 */

use App\Models\Pelayanan\PelayananBphtb\PelayananBphtb;
use App\Models\Pelayanan\RiwayatDitolakBphtb\RiwayatDitolakBphtb;
use App\Models\Sppt\Sppt;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\MasterData\Npoptkp\Npoptkp;
use App\Models\DatOpBumi\DatOpBumi;
use App\Models\DatSubjekPajak\DatSubjekPajak;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Bphtb\BphtbRepositories;

class EloquentBphtbRepositories implements BphtbRepositories
{
    use Message, Response, Notification, Calculation, Converter, Generator, Bphtb;

    private $pelayananBphtb;
    private $storage;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $sppt;
    private $year;
    private $datObjekPajak;
    private $datSubjekPajak;
    private $riwayatDitolakBphtb;
    private $pembayaranSppt;
    private $secondDb;
    private $datetime;
    private $npoptkp;
    private $thirdDb;
    private $datOpBumi;

    public function __construct(
        DatObjekPajak $datObjekPajak,
        Sppt $sppt,
        PelayananBphtb $pelayananBphtb,
        CheckerHelpers $checkerHelpers,
        RiwayatDitolakBphtb $riwayatDitolakBphtb,
        PaginateHelpers $paginateHelpers,
        PembayaranSppt $pembayaranSppt,
        Npoptkp $npoptkp,
        DatOpBumi $datOpBumi,
        DatSubjekPajak $datSubjekPajak
    ) {

        /**
         * initialize model
         */
        $this->pelayananBphtb = $pelayananBphtb;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->sppt = $sppt;
        $this->riwayatDitolakBphtb = $riwayatDitolakBphtb;
        $this->npoptkp = $npoptkp;
        $this->datOpBumi = $datOpBumi;
        $this->datSubjekPajak = $datSubjekPajak;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->storage = path('pelayanan bphtb');
        $this->provinsi = [globalAttribute()['kdProvinsi'], 'SUMATERA UTARA'];
        $this->kabupaten = [globalAttribute()['kdKota'], 'BINJAI'];
        $this->year = Carbon::now()->format('Y');
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->secondDb = DB::connection('second_mysql');
        $this->thirdDb = DB::connection('third_mysql');
    }

    /**
     * store data to db
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {

            /**
             * check duplicate data
             */
            $checkData = $this->pelayananBphtb->where([
                'nop' => $request['nop'],
                'status_verifikasi' => 0
            ])
                ->whereNull('deleted_at')
                ->whereYear('created_at', $this->year)
                ->first();
            if (!is_null($checkData)) :
                throw new \Exception($this->outputMessage('exists', $request['nop']));
            endif;

            /**
             * updating files
             */
            $requestFiles = $this->bptbhUploadingFiles($request, 'store');
            $request = array_merge($request, $requestFiles);

            /**
             * count jumlah bphtb
             */
            $getBphtb = $this->pelayananBphtb->select(DB::raw("COUNT(*) AS total"))
                ->join('jenis_perolehan', 'pelayanan_bphtb.uuid_jenis_perolehan', '=', 'jenis_perolehan.uuid_jenis_perolehan')
                ->where('nik', $request['nik'])
                ->whereNull('deleted_at')
                ->whereRaw('YEAR(pelayanan_bphtb.created_at) = "' . $this->year . '"')
                ->whereNotIn('jenis_perolehan.kode', [5])
                ->orderBy('pelayanan_bphtb.id', 'ASC')
                ->first();

            /**
             * get NPOPTKP
             */
            $getNpoptkp = $this->npoptkp->select('*')
                ->selectRaw('(SELECT kode FROM jenis_perolehan WHERE uuid_jenis_perolehan = npoptkp.uuid_jenis_perolehan) AS kode')
                ->where('uuid_jenis_perolehan', $request['uuid_jenis_perolehan'])
                ->orderBy('id', 'desc')
                ->first();
            if (is_null($getNpoptkp)) :
                throw new \Exception($this->outputMessage('not found', 'NPOPTKP'));
            endif;

            /**
             * get jenis bumi
             */
            $getJenisBumi = $this->datOpBumi->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $request['nop'] . '"')->first();
            if (is_null($getJenisBumi)) :
                throw new \Exception($this->outputMessage('not found', 'OP Bumi dengan NOP ' . $request['nop']));
            endif;

            /**
             * perhitungan NJOP PBB
             */
            $paramNjopPbb = [
                'luas_njop_tanah' => $request['luas_njop_tanah'],
                'luas_njop_bangunan' => $request['luas_njop_bangunan'],
                'nilai_transaksi' => $request['nilai_transaksi'],
                'total' => $getBphtb->total,
                'nilai' => $getNpoptkp->nilai,
                'npoptkp' => null,
                'luas_tanah' => $request['luas_tanah'],
                'luas_bangunan' => $request['luas_bangunan'],
                'kode_jenis_perolehan' => $getNpoptkp->kode
            ];
            $njopPbb = $this->njopPbbForBphtb($paramNjopPbb);

            /**
             * save data pelayanan
             */
            $nilaiBphtb = $getJenisBumi->JNS_BUMI == 4 ? 0 : $njopPbb['nilaiBphtb'];
            $request = array_merge($request, [
                'npop'   => $njopPbb['npop'],
                'npoptkp' => $njopPbb['npoptkp'],
                'npopkp' => $njopPbb['npopkp'],
                'nilai_bphtb' => $nilaiBphtb,
                'luas_tanah' => $request['luas_tanah'],
                'luas_bangunan' => $request['luas_bangunan'],
                'luas_njop_tanah' => $request['luas_njop_tanah'],
                'luas_njop_bangunan' => $request['luas_njop_bangunan'],
                'njop_tanah' => $njopPbb['njopTanah'],
                'njop_bangunan' => $njopPbb['njopBangunan'],
            ]);

            $saveData = $this->pelayananBphtb->create($request);
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', 'BPHTB dengan nomor registrasi ' . $request['no_registrasi']));
            endif;

            /**
             * whatsapp notification
             */
            $getSetting = $this->checkerHelpers->settingChecker('whatsapp notif');
            if (is_null($getSetting)) :
                throw new \Exception($this->outputMessage('not found', 'whatsapp notification setting'));
            endif;

            /**
             * send wa not if enabled
             */
            if ($getSetting->description == 'enabled') :

                /**
                 * get data kasubbid
                 */
                $getKasubbid = $this->checkerHelpers->userChecker(['role' => 'kasubbid']);
                if (is_null($getKasubbid)) :
                    throw new \Exception($this->outputMessage('not found', 'Kasubbid'));
                endif;

                /**
                 * get tanggal pendaftaran
                 */
                $getCurrentPelayanan = $this->checkerHelpers->pelayananBphtbChecker(['no_registrasi' => $request['no_registrasi']]);
                $tanggalPendaftaran = Carbon::parse($getCurrentPelayanan->created_at)->locale('id');
                $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);

                /**
                 * send whatsapp notification
                 */
                $message = "Permohonan BPHTB";
                $message .= "\nAtas nama " . $request['nama_wp_2'];
                $message .= "\nNo Registrasi " . $request['no_registrasi'];
                $message .= "\n" . $tanggalPendaftaran->format('l, j F Y ; h:i:s ') . "\n\n";
                $message .= "\nMohon segera diproses";
                $message .= "\n(OPERATOR)";
                $message .= "\n _BPKPAD KOTA BINJAI_";
                $callBack = $this->whatsapp($getKasubbid->no_hp, $message);

                /**
                 * fail send whatsapp notification
                 */
                if ($callBack->status != true) :
                    throw new \Exception($this->outputMessage('unsend', 'whatsapp'));
                endif;
            endif;
            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'BPHTB dengan nomor registrasi ' . $request['no_registrasi']));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * all record
     */
    public function data($statusVerifikasi, $pageSize, $deleted)
    {
        try {
            /**
             * data pelayanan
             */
            $statusVerifikasi = $statusVerifikasi == 'all' ? 'status_verifikasi in (0, 1, 2, 3, 4, 5, 0.1, 1.1, 2.1, 0.2, 1.2, 2.2)' : 'status_verifikasi in (' . $statusVerifikasi . ')';
            $data = $this->queryAllData($statusVerifikasi, $pageSize, $deleted);

            /**
             * set response
             */
            $response = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * autocomplete data by nop
     */
    public function autocomplete($nop, $tahun)
    {
        try {

            $nop = $this->provinsi[0] . $this->kabupaten[0] . $nop;

            $getObjekPajak = $this->datObjekPajak->select(
                'NM_WP as nama',
                'NJOP_BUMI as njop_tanah',
                'NJOP_BNG as njop_bangunan',
                'TOTAL_LUAS_BUMI as luas_tanah',
                'TOTAL_LUAS_BNG as luas_bangunan',
                'JALAN_OP as alamat',
                'BLOK_KAV_NO_OP as no_sertifikat'
            )
                ->selectRaw('CAST(CASE WHEN NJOP_BUMI = 0 THEN 0 ELSE (ROUND(NJOP_BUMI / TOTAL_LUAS_BUMI)) END AS UNSIGNED) AS njop_bumi_permeter')
                ->selectRaw('CAST(CASE WHEN NJOP_BNG = 0 THEN 0 ELSE (ROUND(NJOP_BNG / TOTAL_LUAS_BNG)) END AS UNSIGNED) AS njop_bng_permeter')
                ->join('dat_subjek_pajak', 'dat_objek_pajak.SUBJEK_PAJAK_ID', '=', 'dat_subjek_pajak.SUBJEK_PAJAK_ID')
                ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = "' . $nop . '"')
                ->first();
            if (is_null($getObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'data objek pajak'));
            endif;

            $data = $getObjekPajak;
            $data['tahun'] = is_null($tahun) ? $this->year : $tahun;

            $response = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by uuid pelayanan bphtb
     */
    public function get($param)
    {
        try {
            $getData = $this->queryGetSingleData($param);
            $getData = collect($getData)->except(['id']);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'data objek pajak'));
            endif;

            $response = $this->successData($this->outputMessage('data', 1), $getData);
        } catch (\Exception $e) {
            $response = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * update data to db
     */
    public function update($request, $uuidPelayananBphtb)
    {
        $request = collect($request)->except(['_method'])->toArray();
        DB::beginTransaction();
        try {

            /**
             * check if data exist
             */
            $getData = $this->checkerHelpers->pelayananBphtbChecker(["uuid_pelayanan_bphtb" => $uuidPelayananBphtb]);
            $files = [
                'ktp' => isset($getData->ktp) ? $getData->ktp : null,
                'fotoOp' => isset($getData->foto_op) ? $getData->foto_op : null,
                'sertifikatTanah' => isset($getData->sertifikat_tanah) ? $getData->sertifikat_tanah : null,
                'fcSpptThnBerjalan' => isset($getData->fc_sppt_thn_berjalan) ? $getData->fc_sppt_thn_berjalan : null,
                'fcSkJualBeli' => isset($getData->fc_sk_jual_beli) ? $getData->fc_sk_jual_beli : null,
                'perjalanKredit' => isset($getData->perjanjian_kredit) ? $getData->perjanjian_kredit : null,
                'suratPernyataan' => isset($getData->surat_pernyataan) ? $getData->surat_pernyataan : null,
                'fcSuratKematian' => isset($getData->fc_surat_kematian) ? $getData->fc_surat_kematian : null,
                'fcSkAhliWaris' => isset($getData->fc_sk_ahli_waris) ? $getData->fc_sk_ahli_waris : null,
                'spGantiRugi' => isset($getData->sp_ganti_rugi) ? $getData->sp_ganti_rugi : null,
                'skBpn' => isset($getData->sk_bpn) ? $getData->sk_bpn : null,
                'fcSkHibahDesa' => isset($getData->fc_sk_hibah_desa) ? $getData->fc_sk_hibah_desa : null,
                'risalahLelang' => isset($getData->risalah_lelang) ? $getData->risalah_lelang : null,
            ];

            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'BPHTB'));
            endif;

            /**
             * check jenis perolehan
             */
            $getJenisPerolehan = $this->checkerHelpers->jenisPerolehanChecker(['uuid_jenis_perolehan' => $getData->uuid_jenis_perolehan]);
            if (is_null($getJenisPerolehan)) :
                throw new \Exception($this->outputMessage('not found', 'Jenis Perolehan'));
            endif;

            /**
             * updating files
             */
            $requestFiles = $this->bptbhUploadingFiles($request, 'update', $files);
            $request = array_merge($request, $requestFiles);

            /**
             * perhitungan NJOP PBB / SPPT
             */
            $paramNjopPbb = [
                'luas_njop_tanah' => $request['luas_njop_tanah'],
                'luas_njop_bangunan' => $request['luas_njop_bangunan'],
                'nilai_transaksi' => $request['nilai_transaksi'],
                'total' => 0,
                'nilai' => 0,
                'npoptkp' => $getData->npoptkp,
                'luas_tanah' => $request['luas_tanah'],
                'luas_bangunan' => $request['luas_bangunan'],
                'kode_jenis_perolehan' => $getJenisPerolehan->kode
            ];
            $njopPbb = $this->njopPbbForBphtb($paramNjopPbb);

            /**
             * updating pelayanan bphtb
             */
            $request = array_merge($request, [
                'npop'   => $njopPbb['npop'],
                'npoptkp' => $njopPbb['npoptkp'],
                'npopkp' => $njopPbb['npopkp'],
                'nilai_bphtb' => $njopPbb['nilaiBphtb'],
                'luas_tanah' => $request['luas_tanah'],
                'luas_bangunan' => $request['luas_bangunan'],
                'luas_njop_tanah' => $request['luas_njop_tanah'],
                'luas_njop_bangunan' => $request['luas_njop_bangunan'],
                'njop_tanah' => $njopPbb['njopTanah'],
                'njop_bangunan' => $njopPbb['njopBangunan'],
                'updated_by' => authAttribute()['id']
            ]);
            $updateData = $this->pelayananBphtb->where(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb])->update($request);
            if (!$updateData) :
                throw new \Exception($this->outputMessage('update fail', 'BPHTB dengan nomor registrasi ' . $getData->no_registrasi));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', 'BPHTB dengan nomor registrasi ' . $getData->no_registrasi));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * update status verifikasi
     */
    public function updateStatusVerifikasi($request, $uuidPelayananBphtb)
    {
        DB::beginTransaction();
        try {

            /**
             * check if data exist
             */
            $getData = $this->checkerHelpers->pelayananBphtbChecker(["uuid_pelayanan_bphtb" => $uuidPelayananBphtb]);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'BPHTB'));
            endif;

            /**
             * check sts history
             */
            $checkStsHitory = $this->secondDb->table('STS_History')->where([
                'No_Pokok_WP' => $getData->nop,
                'Status_Bayar' => 0,
                'Tahun' => $this->year
            ])
                ->orWhere(function ($query) use ($getData) {
                    $query->where([
                        'No_Pokok_WP' => $getData->nop,
                        'Nilai' => 0,
                        'Kode_Cab' => 'BPKPAD',
                        'Nama_Channel' => 'NIHIL',
                        'Tahun' => $this->year
                    ]);
                })
                ->orWhere(function ($query) use ($getData) {
                    $query->where([
                        'No_STS' => $getData->no_sts,
                        'Status_Bayar' => 1,
                        'Jn_Pajak' => '410115',
                        'Nm_Pajak' => 'BPHTB'
                    ]);
                })
                ->first();

            /**
             * generate no sts
             */
            $noSts = $this->noSts();

            /**
             * insert into reject histoty
             */
            if (!empty($request['no_registrasi'])) :
                $requestRejectHistory = collect($request)->except(['status_verifikasi'])->toArray();
                $saveRejectHistory = $this->riwayatDitolakBphtb->create($requestRejectHistory);
                if (!$saveRejectHistory) :
                    throw new \Exception($this->outputMessage('unsaved', 'riwayat ditolak BPHTB'));
                endif;
            endif;

            /**
             * check duplicate sts before insert
             * and generate new no sts
             */
            $checkSts = $this->secondDb->table('STS_History')->where('no_sts', $noSts)->first();
            if ($checkSts) :
                $noSts = $this->noSts();
            endif;

            /**
             * jika status verifiksai 2 / 3
             * maka akan membuat sts history
             */
            if ($request['status_verifikasi'] == 2 || $request['status_verifikasi'] == 3) :
                if (is_null($checkStsHitory)) :

                    /**
                     * get jenis bumi
                     */
                    $getJenisBumi = $this->datOpBumi->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $getData->nop . '"')->first();
                    if (is_null($getJenisBumi)) :
                        throw new \Exception($this->outputMessage('not found', 'Data OP Bumi dengan NOP ' . $getData->nop));
                    endif;

                    /**
                     * extra sts value
                     */
                    $extraStsValue = [];
                    if ($getData->nilai_bphtb > 0 && $getJenisBumi->JNS_BUMI != 4) :
                        $nilaiBphtb = $getData->nilai_bphtb;
                    else :
                        $nilaiBphtb = $getData->nilai_bphtb;
                        $extraStsValue = [
                            'Status_Bayar' => 1,
                            'Tgl_Bayar' => $this->datetime,
                            'Kode_Pengesahan' => 'BJI' . substr($getData->no_registrasi, 4, 12),
                            'Nama_Channel' => 'NIHIL',
                            'Kode_Cab' => 'BPKPAD'
                        ];
                    endif;

                    /**
                     * set value
                     */
                    $nilaiBphtb = is_null($getData->nilai_bphtb_pengurangan) || $getData->nilai_bphtb_pengurangan == 0 ? $nilaiBphtb : $getData->nilai_bphtb_pengurangan;
                    $stsValue = [
                        'Tahun' => $this->year,
                        'No_STS' => $noSts,
                        'Tgl_STS' => $this->datetime,
                        'No_NOP' => $this->nopStsBphtb(),
                        'No_Pokok_WP' => $getData->nop,
                        'Nama_Pemilik' => $getData->nama_wp_2,
                        'Alamat_Pemilik' => $getData->alamat_wp_2,
                        'Jn_Pajak' => globalAttribute()['stsBphtb'],
                        'Nm_Pajak' => 'BPHTB',
                        'Nilai' => $nilaiBphtb
                    ];
                    $stsValue  = array_merge($stsValue, $extraStsValue);

                    /**
                     * save to sts
                     */
                    $saveSts = $this->secondDb->table('STS_History')->insert($stsValue);
                    if (!$saveSts) :
                        throw new \Exception($this->outputMessage('unsaved', 'STS History'));
                    endif;
                endif;
            endif;

            /**
             * set request
             */
            $requestBphtb = collect($request)->except(['no_registrasi', 'uuid_user', 'keterangan', 'harga'])->toArray();
            if (($request['status_verifikasi'] == 2 || $request['status_verifikasi'] == 3) && is_null($checkStsHitory)) :
                $requestBphtb = array_merge($requestBphtb, ['no_sts' => $noSts]);
            else :
                $requestBphtb = $requestBphtb;
            endif;

            /**
             * hitung pengurangan
             */
            if (isset($request['pengurangan'])) :
                $requestBphtb['nilai_bphtb_pengurangan'] = $this->penguranganBphtb($getData->nilai_bphtb, $request['pengurangan']);
            endif;

            /**
             * update data bphtb
             */
            $requestBphtb['updated_by'] = authAttribute()['id'];
            $updateBphtb = $this->pelayananBphtb->where(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb])->update($requestBphtb);
            if (!$updateBphtb) :
                throw new \Exception($this->outputMessage('update fail', 'status verifikasi'));
            endif;

            /**
             * update data sts
             */
            if (isset($requestBphtb['nilai_bphtb_pengurangan']) && !is_null($getData->no_sts)) :
                $updateSts = $this->secondDb->table('STS_History')->where([
                    'No_Pokok_WP' => $getData->nop,
                    'No_STS' => $getData->no_sts,
                ])
                    ->update(['Nilai' => $requestBphtb['nilai_bphtb_pengurangan']]);
                if (!$updateSts) :
                    throw new \Exception($this->outputMessage('update fail', 'STS History'));
                endif;
            endif;

            /**
             * jika status verifiksai 4
             */
            if ($request['status_verifikasi'] == 4 || $request['status_verifikasi'] == 0.1 || $request['status_verifikasi'] == 1.1 || $request['status_verifikasi'] == 2.1) :

                /**
                 * check sts history
                 */
                $getSts = $this->secondDb->table('STS_History')->where('No_Pokok_WP', $getData->nop)->first();
                if (!is_null($getSts)) :
                    $updateBphtb = $this->pelayananBphtb->where('uuid_pelayanan_bphtb', $uuidPelayananBphtb)->update(['no_sts' => null]);
                    $deleteSts = $this->secondDb->table('STS_History')->where('No_Pokok_WP', $getData->nop)->delete();
                    if (!$deleteSts && !$updateBphtb) :
                        throw new \Exception($this->outputMessage('undeleted', 'STS History'));
                    endif;
                endif;
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', 'status verifikasi'));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }
        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * riwayat ditolak
     */
    public function riwayatDitolak($noRegistrasi)
    {
        try {
            /**
             * data pelayanan
             */
            $data = $this->riwayatDitolakBphtb->select(
                "keterangan",
                "harga",
                "name as nama",
                DB::raw("DATE(riwayat_ditolak_bphtb.created_at) as tanggal")
            )
                ->join('users', 'riwayat_ditolak_bphtb.uuid_user', '=', 'users.uuid_user')
                ->orderBy('riwayat_ditolak_bphtb.id', 'desc')
                ->where("no_registrasi", $noRegistrasi)
                ->get();

            /**
             * set response
             */
            $response = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * detail
     */
    public function detail($uuidPelayananBphtb)
    {
        try {
            $data = $this->queryDetail($uuidPelayananBphtb);
            $data = collect($data)->except([
                'kaban', 'b.letak_tanah_bangunan', 'b.kelurahan', 'b.kecamatan', 'b.rt', 'b.kabupaten', 'id'
            ]);
            $response = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * store status ditolak
     */
    public function storeStatusDitolak($request)
    {
        DB::beginTransaction();
        try {

            /**
             * get nop from no registrasi
             */
            $getNop = $this->checkerHelpers->pelayananBphtbChecker(['no_registrasi' => $request['no_registrasi']]);
            if (is_null($getNop)) :
                throw new \Exception($this->outputMessage('not found', 'BPHTB'));
            endif;

            /**
             * save data pelayanan
             */
            $updateData = $this->pelayananBphtb->where('no_registrasi', $request['no_registrasi'])->update(['status_verifikasi' => 4]);
            if (!$updateData) :
                throw new \Exception($this->outputMessage('unsaved', 'perubahan status verifikasi dengan nomor registrasi ' . $request['no_registrasi']));
            endif;

            /**
             * check sts history
             */
            $getSts = $this->secondDb->table('STS_History')->where('No_Pokok_WP', $getNop->nop)->first();
            if (!is_null($getSts)) :
                $deleteSts = $this->secondDb->table('STS_History')->where('No_Pokok_WP', $getNop->nop)->delete();
                if (!$deleteSts) :
                    throw new \Exception($this->outputMessage('undeleted', 'STS History'));
                endif;
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'BPHTB dengan nomor registrasi ' . $request['no_registrasi']));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * all record by no registrasi
     */
    public function search($condition, $pageSize, $deleted)
    {
        try {
            /**
             * data pelayanan
             */
            $statusVerifikasi = "status_verifikasi in (" . $condition['status_verifikasi'] . ")";
            $conditions = $condition['column'] == 'created_by' ? $condition['column'] . ' = "' . $condition['value'] . '"' : $condition['column'] . " LIKE '%" . $condition['value'] . "%'";
            $conditions = $condition['status_verifikasi'] != "all" ? $conditions . " AND " . $statusVerifikasi : $conditions;
            $data = $this->queryAllData($conditions, $pageSize, $deleted);

            /**
             * set response
             */
            $response = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * update perhitungan NJOP PBB
     */
    public function updatePerhitunganNjop($request, $uuidPelayananBphtb)
    {
        DB::beginTransaction();
        try {
            $request = collect($request)->except(['_method'])->toArray();
            /**
             * check if data exist
             */
            $getData = $this->checkerHelpers->pelayananBphtbChecker(["uuid_pelayanan_bphtb" => $uuidPelayananBphtb]);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'BPHTB'));
            endif;

            /**
             * get sppt
             */
            $getSppt = $this->sppt->select(
                'RW_WP_SPPT',
                'RT_WP_SPPT',
                'LUAS_BUMI_SPPT',
                'LUAS_BNG_SPPT',
                'NJOP_BUMI_SPPT',
                'NJOP_BNG_SPPT',
                'NJOP_SPPT',
                'sppt.KD_DATI2',
                'sppt.KD_KECAMATAN',
                'sppt.KD_KELURAHAN',
                'NM_DATI2',
                'NM_KELURAHAN',
                'NM_KECAMATAN',
                'NM_WP_SPPT'
            )
                ->whereRaw('CONCAT(sppt.KD_PROPINSI, sppt.KD_DATI2, sppt.KD_KECAMATAN, sppt.KD_KELURAHAN, sppt.KD_BLOK, NO_URUT, sppt.KD_JNS_OP) = "' . $getData->nop . '"')
                ->leftJoin('ref_dati2', 'sppt.KD_DATI2', '=', 'ref_dati2.KD_DATI2')
                ->leftJoin('ref_kecamatan', 'sppt.KD_KECAMATAN', '=', 'ref_kecamatan.KD_KECAMATAN')
                ->leftJoin('ref_kelurahan', 'sppt.KD_KELURAHAN', '=', 'ref_kelurahan.KD_KELURAHAN')
                ->orderBy('THN_PAJAK_SPPT', 'desc')
                ->first();
            if (is_null($getSppt)) :
                throw new \Exception($this->outputMessage('not found', 'SPPT'));
            endif;

            /**
             * perhitungan NJOP PBB
             */
            $njopBangunan = !is_null($getData->njop_bangunan) ? $getData->njop_bangunan : ($getSppt->NJOP_BNG_SPPT == 0 ? 0 : $getSppt->NJOP_BNG_SPPT / $getSppt->LUAS_BNG_SPPT);
            $njopTanah = !is_null($getData->njop_tanah) ? $getData->njop_tanah : ($getSppt->NJOP_BUMI_SPPT == 0 ? 0 : $getSppt->NJOP_BUMI_SPPT / $getSppt->LUAS_BUMI_SPPT);
            $luasNjopTanah  = $request['luas_tanah'] * $njopTanah;
            $luasNjopBangunan  = $request['luas_bangunan'] * $njopBangunan;
            $njopPbb    = $luasNjopTanah + $luasNjopBangunan;
            $npop       = $njopPbb > $getData->nilai_transaksi ? $njopPbb : $getData->nilai_transaksi;
            $npoptkp    = $getData->npoptkp;
            $npopkp     = $npop - $npoptkp;
            $nilaiBphtb = $this->bphtbTerhutang($npopkp);

            /**
             * save data pelayanan
             */
            $request = array_merge($request, [
                'npop'   => $npop,
                'npoptkp' => $npoptkp,
                'npopkp' => $npopkp,
                'nilai_bphtb' => $nilaiBphtb,
                'luas_tanah' => $request['luas_tanah'],
                'luas_bangunan' => $request['luas_bangunan'],
                'luas_njop_tanah' => $luasNjopTanah,
                'luas_njop_bangunan' => $luasNjopBangunan,
                'njop_tanah' => $njopTanah,
                'njop_bangunan' => $njopBangunan,
                'updated_by' => authAttribute()['id']
            ]);

            /**
             * update data pelayanan
             */
            $updateData = $this->pelayananBphtb->where(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb])->update($request);
            if (!$updateData) :
                throw new \Exception($this->outputMessage('update fail', 'BPHTB dengan nomor registrasi ' . $getData->no_registrasi));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', 'BPHTB dengan nomor registrasi ' . $getData->no_registrasi));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * delete dokumen
     */
    public function deleteDokumen($uuidPelayananBphtb, $dokumen)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData   = $this->checkerHelpers->pelayananBphtbChecker(["uuid_pelayanan_bphtb" => $uuidPelayananBphtb]);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'BPHTB'));
            endif;
            $photoFile  = $getData[$dokumen];

            /**
             * remove foto
             */
            if (!is_null($photoFile)) :
                if (file_exists($this->storage . "/" . $photoFile)) :
                    if (!unlink($this->storage . "/" . $photoFile)) :
                        throw new \Exception($this->outputMessage('remove fail', $photoFile));
                    endif;
                endif;
            endif;

            /**
             * delete data
             */
            $delete = $this->pelayananBphtb->where('uuid_pelayanan_bphtb', $uuidPelayananBphtb)->update([$dokumen => NULL]);
            if (!$delete) :
                throw new \Exception($this->outputMessage('undeleted', 'Dokumen ' . str_replace('_', ' ', $dokumen) . ''));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('deleted', 'Dokumen ' . str_replace('_', ' ', $dokumen) . ''));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * update perhitungan BPHTB
     */
    public function updatePerhitunganBphtb($request, $uuidPelayananBphtb)
    {
        DB::beginTransaction();
        try {
            $request = collect($request)->except(['_method'])->toArray();
            /**
             * check if data exist
             */
            $getData = $this->checkerHelpers->pelayananBphtbChecker(["uuid_pelayanan_bphtb" => $uuidPelayananBphtb]);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'BPHTB'));
            endif;

            /**
             * get sppt
             */
            $getSppt = $this->sppt->select(
                'RW_WP_SPPT',
                'RT_WP_SPPT',
                'LUAS_BUMI_SPPT',
                'LUAS_BNG_SPPT',
                'NJOP_BUMI_SPPT',
                'NJOP_BNG_SPPT',
                'NJOP_SPPT',
                'sppt.KD_DATI2',
                'sppt.KD_KECAMATAN',
                'sppt.KD_KELURAHAN',
                'NM_DATI2',
                'NM_KELURAHAN',
                'NM_KECAMATAN',
                'NM_WP_SPPT'
            )
                ->whereRaw('CONCAT(sppt.KD_PROPINSI, sppt.KD_DATI2, sppt.KD_KECAMATAN, sppt.KD_KELURAHAN, sppt.KD_BLOK, NO_URUT, sppt.KD_JNS_OP) = "' . $getData->nop . '"')
                ->leftJoin('ref_dati2', 'sppt.KD_DATI2', '=', 'ref_dati2.KD_DATI2')
                ->leftJoin('ref_kecamatan', 'sppt.KD_KECAMATAN', '=', 'ref_kecamatan.KD_KECAMATAN')
                ->leftJoin('ref_kelurahan', 'sppt.KD_KELURAHAN', '=', 'ref_kelurahan.KD_KELURAHAN')
                ->orderBy('THN_PAJAK_SPPT', 'desc')
                ->first();
            if (is_null($getSppt)) :
                throw new \Exception($this->outputMessage('not found', 'SPPT'));
            endif;

            /**
             * perhitungan NJOP PBB
             */
            $njopBangunan = !is_null($getData->njop_bangunan) ? $getData->njop_bangunan : ($getSppt->NJOP_BNG_SPPT == 0 ? 0 : $getSppt->NJOP_BNG_SPPT / $getSppt->LUAS_BNG_SPPT);
            $njopTanah = !is_null($getData->njop_tanah) ? $getData->njop_tanah : ($getSppt->NJOP_BUMI_SPPT == 0 ? 0 : $getSppt->NJOP_BUMI_SPPT / $getSppt->LUAS_BUMI_SPPT);
            $luasNjopTanah  = $getData->luas_tanah * $njopTanah;
            $luasNjopBangunan  = $getData->luas_bangunan * $njopBangunan;
            $njopPbb    = $luasNjopTanah + $luasNjopBangunan;
            $npop       = $njopPbb > $getData->nilai_transaksi ? $njopPbb : $getData->nilai_transaksi;
            $npoptkp    = $request['npoptkp'];
            $npopkp     = $npop - $npoptkp;
            $nilaiBphtb = $this->bphtbTerhutang($npopkp);

            /**
             * save data pelayanan
             */
            $request = array_merge($request, [
                'npop'   => $npop,
                'npoptkp' => $npoptkp,
                'npopkp' => $npopkp,
                'nilai_bphtb' => $nilaiBphtb,
                'luas_tanah' => $getData->luas_tanah,
                'luas_bangunan' => $getData->luas_bangunan,
                'luas_njop_tanah' => $luasNjopTanah,
                'luas_njop_bangunan' => $luasNjopBangunan,
                'njop_tanah' => $njopTanah,
                'njop_bangunan' => $njopBangunan,
                'updated_by' => authAttribute()['id']
            ]);

            /**
             * update data pelayanan
             */
            $updateData = $this->pelayananBphtb->where(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb])->update($request);
            if (!$updateData) :
                throw new \Exception($this->outputMessage('update fail', 'BPHTB dengan nomor registrasi ' . $getData->no_registrasi));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', 'BPHTB dengan nomor registrasi ' . $getData->no_registrasi));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * delete data
     */
    public function delete($uuidPelayananBphtb)
    {
        DB::beginTransaction();
        try {
            /**
             * check if data exist
             */
            $getData = $this->checkerHelpers->pelayananBphtbChecker(["uuid_pelayanan_bphtb" => $uuidPelayananBphtb]);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'BPHTB'));
            endif;

            /**
             * delete
             */
            $request = [
                'deleted_by' => authAttribute()['id'],
                'deleted_at' => $this->datetime
            ];
            $deleteData = $this->pelayananBphtb->where(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb])->update($request);
            if (!$deleteData) :
                throw new \Exception($this->outputMessage('undeleted', 'BPHTB berhasil dihapus'));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('deleted', 'BPHTB berhasil dihapus'));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * store status ditolak
     */
    public function storePembayaranManual($request)
    {
        DB::beginTransaction();
        try {

            /**
             * check sts history
             */
            $request['Jn_Pajak'] = globalAttribute()['stsBphtb'];
            $request['Nm_Pajak'] = 'BPHTB';
            $request['Status_Bayar'] = 1;
            $request['Nama_Channel'] = 'BPKPADSSPD';
            $request['Kode_Cab'] = 'BPHTB';
            $saveSts = $this->secondDb->table('STS_History')->insert($request);
            if (!$saveSts) :
                throw new \Exception($this->outputMessage('unsaved', 'STS History'));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('saved', `STS dengan nomor STS {$request['No_STS']}`));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * update full data to db
     */
    public function updateFull($request, $uuidPelayananBphtb)
    {
        $request = collect($request)->except(['_method'])->toArray();
        DB::beginTransaction();
        try {

            /**
             * check if data exist
             */
            $getData = $this->checkerHelpers->pelayananBphtbChecker(["uuid_pelayanan_bphtb" => $uuidPelayananBphtb]);
            $files = [
                'ktp' => isset($getData->ktp) ? $getData->ktp : null,
                'fotoOp' => isset($getData->foto_op) ? $getData->foto_op : null,
                'sertifikatTanah' => isset($getData->sertifikat_tanah) ? $getData->sertifikat_tanah : null,
                'fcSpptThnBerjalan' => isset($getData->fc_sppt_thn_berjalan) ? $getData->fc_sppt_thn_berjalan : null,
                'fcSkJualBeli' => isset($getData->fc_sk_jual_beli) ? $getData->fc_sk_jual_beli : null,
                'perjalanKredit' => isset($getData->perjanjian_kredit) ? $getData->perjanjian_kredit : null,
                'suratPernyataan' => isset($getData->surat_pernyataan) ? $getData->surat_pernyataan : null,
                'fcSuratKematian' => isset($getData->fc_surat_kematian) ? $getData->fc_surat_kematian : null,
                'fcSkAhliWaris' => isset($getData->fc_sk_ahli_waris) ? $getData->fc_sk_ahli_waris : null,
                'spGantiRugi' => isset($getData->sp_ganti_rugi) ? $getData->sp_ganti_rugi : null,
                'skBpn' => isset($getData->sk_bpn) ? $getData->sk_bpn : null,
                'fcSkHibahDesa' => isset($getData->fc_sk_hibah_desa) ? $getData->fc_sk_hibah_desa : null,
                'risalahLelang' => isset($getData->risalah_lelang) ? $getData->risalah_lelang : null,
            ];

            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'BPHTB'));
            endif;

            /**
             * check jenis perolehan
             */
            $getJenisPerolehan = $this->checkerHelpers->jenisPerolehanChecker(['uuid_jenis_perolehan' => $getData->uuid_jenis_perolehan]);
            if (is_null($getJenisPerolehan)) :
                throw new \Exception($this->outputMessage('not found', 'Jenis Perolehan'));
            endif;

            /**
             * updating files
             */
            $requestFiles = $this->bptbhUploadingFiles($request, 'update', $files);
            $request = array_merge($request, $requestFiles);

            /**
             * updating pelayanan bphtb
             */
            $request['updated_by'] = authAttribute()['id'];
            $updateData = $this->pelayananBphtb->where(['uuid_pelayanan_bphtb' => $uuidPelayananBphtb])->update($request);
            if (!$updateData) :
                throw new \Exception($this->outputMessage('update fail', 'BPHTB dengan nomor registrasi ' . $getData->no_registrasi));
            endif;

            /**
             * updating sts history
             */
            $noSTS = $getData->no_sts;
            $noNOP = $getData->nop;
            $nilaiBphtbPengurangan = $getData->nilai_bphtb_pengurangan;
            $nilaiBpthb = !is_null($nilaiBphtbPengurangan) || !empty($nilaiBphtbPengurangan) || $nilaiBphtbPengurangan != '' ? $nilaiBphtbPengurangan : $getData->nilai_bphtb;
            if (!is_null($noSTS) || !empty($noSTS) || $noSTS != '' && ($noSTS != $request['no_sts'])) :

                /**
                 * check previous STS
                 */
                $checkPreviousSTS = $this->secondDb->table('STS_History')->where([
                    'No_STS' => $noSTS,
                    'No_Pokok_WP' => $noNOP,
                ])->first();
                if (is_null($checkPreviousSTS)) :
                    throw new \Exception($this->outputMessage('not found', `STS History dengan No STS {$noSTS}`));
                endif;

                /**
                 * check current STS
                 */
                $checkCurrentSTS = $this->secondDb->table('STS_History')->where([
                    'No_STS' => $noSTS,
                    'No_Pokok_WP' => $noNOP,
                    'Nilai' => $nilaiBpthb
                ])->first();
                if (!is_null($checkCurrentSTS)) :

                    /**
                     * update STS History
                     */
                    $updateSTS = $this->secondDb->table('STS_History')->where([
                        'No_NOP' => $checkCurrentSTS->No_NOP
                    ])->update([
                        'No_STS' => $noSTS,
                        'No_Pokok_WP' => $noNOP,
                        'Nilai' => $nilaiBpthb
                    ]);
                    if (!$updateSTS) :
                        throw new \Exception($this->outputMessage('update fail', `STS History dengan No STS {$noSTS}`));
                    endif;
                endif;
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', 'BPHTB dengan nomor registrasi ' . $getData->no_registrasi));
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

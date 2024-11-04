<?php

namespace App\Repositories\Pelayanan\Pbb\Pendaftaran;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Carbon;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Notification;
use App\Traits\Generator;
use App\Traits\Briva;
use App\Traits\Calculation;
use App\Traits\CalculateLspop;

/**
 * import models
 */

use App\Models\Pelayanan\Pelayanan\Pelayanan;
use App\Models\Pelayanan\Lspop\Lspop;
use App\Models\Sppt\Sppt;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\DatSubjekPajak\DatSubjekPajak;
use App\Models\DatOpBumi\DatOpBumi;
use App\Models\DatOpBangunan\DatOpBangunan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Pbb\Pendaftaran\PendaftaranRepositories;

class EloquentPendaftaranRepositories implements PendaftaranRepositories
{
    use Message, Response, Notification, Generator, Briva, Calculation, CalculateLspop;

    private $pelayanan;
    private $lspop;
    private $storage;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $sppt;
    private $year;
    private $datObjekPajak;
    private $datSubjekPajak;
    private $idUser;
    private $roleUser;
    private $datOpBumi;
    private $datOpBangunan;
    private $datetime;

    public function __construct(
        Pelayanan $pelayanan,
        Lspop $lspop,
        Sppt $sppt,
        DatObjekPajak $datObjekPajak,
        DatSubjekPajak $datSubjekPajak,
        DatOpBumi $datOpBumi,
        DatOpBangunan $datOpBangunan,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers
    ) {
        /**
         * initialize model
         */
        $this->pelayanan = $pelayanan;
        $this->lspop = $lspop;
        $this->sppt = $sppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->datSubjekPajak = $datSubjekPajak;
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
        $this->storage   = path('pelayanan');
        $this->provinsi  = [globalAttribute()['kdProvinsi'], 'SUMATERA UTARA'];
        $this->kabupaten = [globalAttribute()['kdKota'], 'BINJAI'];
        $this->year      = Carbon::now()->format('Y');
        $this->idUser    = authAttribute()['id'];
        $this->roleUser  = authAttribute()['role'];
        $this->datetime = Carbon::now()->toDateTimeLocalString();
    }

    /**
     * store data to db
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            /**
             * form
             */
            $request['op_kd_provinsi'] = $this->provinsi[0];
            $request['op_kd_kabupaten'] = $this->kabupaten[0];
            $request['created_by'] = authAttribute()['id'];

            /**
             * fc surat tanah
             */
            if (isset($_FILES['fc_surat_tanah'])) :
                $fcSuratTanahName        = $_FILES['fc_surat_tanah']['name'];
                $fcSuratTanahTempName    = $_FILES['fc_surat_tanah']['tmp_name'];
                $fcSuratTanahExt         = explode('.', $fcSuratTanahName);
                $fcSuratTanahActualExt   = strtolower(end($fcSuratTanahExt));
                $fcSuratTanahNew         = Uuid::uuid4()->getHex() . "." . $fcSuratTanahActualExt;
                $fcSuratTanahDestination = $this->storage . '/' . $fcSuratTanahNew;
                if (!move_uploaded_file($fcSuratTanahTempName, $fcSuratTanahDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['fc_surat_tanah'] = $fcSuratTanahNew;
            endif;

            /**
             * ktp pemilik
             */
            if (isset($_FILES['ktp_pemilik'])) :
                $ktpPemilikName        = $_FILES['ktp_pemilik']['name'];
                $ktpPemilikTempName    = $_FILES['ktp_pemilik']['tmp_name'];
                $ktpPemilikExt         = explode('.', $ktpPemilikName);
                $ktpPemilikActualExt   = strtolower(end($ktpPemilikExt));
                $ktpPemilikNew         = Uuid::uuid4()->getHex() . "." . $ktpPemilikActualExt;
                $ktpPemilikDestination = $this->storage . '/' . $ktpPemilikNew;
                if (!move_uploaded_file($ktpPemilikTempName, $ktpPemilikDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['ktp_pemilik'] = $ktpPemilikNew;
            endif;

            /**
             * sppt tetangga sebelah
             */
            if (isset($_FILES['sppt_tetangga_sebelah'])) :
                $spptTetanggaSebelahName        = $_FILES['sppt_tetangga_sebelah']['name'];
                $spptTetanggaSebelahTempName    = $_FILES['sppt_tetangga_sebelah']['tmp_name'];
                $spptTetanggaSebelahExt         = explode('.', $spptTetanggaSebelahName);
                $spptTetanggaSebelahActualExt   = strtolower(end($spptTetanggaSebelahExt));
                $spptTetanggaSebelahNew         = Uuid::uuid4()->getHex() . "." . $spptTetanggaSebelahActualExt;
                $spptTetanggaSebelahDestination = $this->storage . '/' . $spptTetanggaSebelahNew;
                if (!move_uploaded_file($spptTetanggaSebelahTempName, $spptTetanggaSebelahDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['sppt_tetangga_sebelah'] = $spptTetanggaSebelahNew;
            endif;

            /**
             * foto objek pajak
             */
            if (isset($_FILES['foto_objek_pajak'])) :
                $fotoObjekPajakName        = $_FILES['foto_objek_pajak']['name'];
                $fotoObjekPajakTempName    = $_FILES['foto_objek_pajak']['tmp_name'];
                $fotoObjekPajakExt         = explode('.', $fotoObjekPajakName);
                $fotoObjekPajakActualExt   = strtolower(end($fotoObjekPajakExt));
                $fotoObjekPajakNew         = Uuid::uuid4()->getHex() . "." . $fotoObjekPajakActualExt;
                $fotoObjekPajakDestination = $this->storage . '/' . $fotoObjekPajakNew;
                if (!move_uploaded_file($fotoObjekPajakTempName, $fotoObjekPajakDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['foto_objek_pajak'] = $fotoObjekPajakNew;
            endif;

            /**
             * spop
             */
            if (isset($_FILES['spop'])) :
                $spopName        = $_FILES['spop']['name'];
                $spopTempName    = $_FILES['spop']['tmp_name'];
                $spopExt         = explode('.', $spopName);
                $spopActualExt   = strtolower(end($spopExt));
                $spopNew         = Uuid::uuid4()->getHex() . "." . $spopActualExt;
                $spopDestination = $this->storage . '/' . $spopNew;
                if (!move_uploaded_file($spopTempName, $spopDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['spop'] = $spopNew;
            endif;

            /**
             * lspop
             */
            if (isset($_FILES['lspop'])) :
                $lspopName        = $_FILES['lspop']['name'];
                $lspopTempName    = $_FILES['lspop']['tmp_name'];
                $lspopExt         = explode('.', $lspopName);
                $lspopActualExt   = strtolower(end($lspopExt));
                $lspopNew         = Uuid::uuid4()->getHex() . "." . $lspopActualExt;
                $lspopDestination = $this->storage . '/' . $lspopNew;
                if (!move_uploaded_file($lspopTempName, $lspopDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['lspop'] = $lspopNew;
            endif;

            /**
             * save data pelayanan
             */
            $saveData = $this->pelayanan->create($request);
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', 'permohonan data baru dengan nomor pelayanan ' . $request['nomor_pelayanan']));
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
            //         throw new \Exception($this->outputMessage('not found', 'kasubbid'));
            //     endif;

            //     /**
            //      * get tanggal pendaftaran
            //      */
            //     $getCurrentPelayanan = $this->checkerHelpers->pelayananChecker(['nomor_pelayanan' => $request['nomor_pelayanan']]);
            //     $tanggalPendaftaran = Carbon::parse($getCurrentPelayanan->created_at)->locale('id');
            //     $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);

            //     /**
            //      * send whatsapp notification
            //      */
            //     $message = "Permohonan Pendaftaran Baru";
            //     $message .= "\nAtas nama " . $request['nama_lengkap'];
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
            $response  = $this->success($this->outputMessage('saved', 'permohonan data baru dengan nomor pelayanan ' . $request['nomor_pelayanan']));
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
     * store data to db
     */
    public function storeLspop($request)
    {
        DB::beginTransaction();
        try {
            /**
             * save data lspop
             */
            $lspopInput = [];
            foreach ($request['no_bangunan'] as $key => $value) :
                $set = [
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
                array_push($lspopInput, $set);
            endforeach;
            $saveData = $this->lspop->insert($lspopInput);
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', 'form LSPOP bangunan dengan nomor pelayanan ' . $request['nomor_pelayanan']));
            endif;

            DB::commit();
            $response  = $this->success($this->outputMessage('saved', 'form LSPOP bangunan dengan nomor pelayanan ' . $request['nomor_pelayanan']));
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
     * get single data
     */
    public function get($param)
    {
        try {
            /**
             * data pelayanan
             */
            $getPelayanan = $this->pelayanan->select("pelayanan.*")
                ->selectRaw('CASE WHEN fc_surat_tanah IS NULL THEN NULL ELSE CONCAT("' . url($this->storage) . '/", fc_surat_tanah) END AS fc_surat_tanah')
                ->selectRaw('CASE WHEN ktp_pemilik IS NULL THEN NULL ELSE CONCAT("' . url($this->storage) . '/", ktp_pemilik) END AS ktp_pemilik')
                ->selectRaw('CASE WHEN sppt_tetangga_sebelah IS NULL THEN NULL ELSE CONCAT("' . url($this->storage) . '/", sppt_tetangga_sebelah) END AS sppt_tetangga_sebelah')
                ->selectRaw('CASE WHEN foto_objek_pajak IS NULL THEN NULL ELSE CONCAT("' . url($this->storage) . '/", foto_objek_pajak) END AS foto_objek_pajak')
                ->selectRaw('CASE WHEN spop IS NULL THEN NULL ELSE CONCAT("' . url($this->storage) . '/", spop) END AS spop')
                ->selectRaw('CASE WHEN lspop IS NULL THEN NULL ELSE CONCAT("' . url($this->storage) . '/", lspop) END AS lspop')
                ->selectRaw('(SELECT layanan FROM layanan WHERE uuid_layanan = pelayanan.uuid_layanan) AS layanan')
                ->selectRaw('(SELECT jenis_layanan FROM jenis_layanan WHERE uuid_jenis_layanan = pelayanan.uuid_jenis_pelayanan) AS jenis_layanan')
                ->where(['pelayanan.uuid_pelayanan' => $param])
                ->orWhere(['nomor_pelayanan' => $param])
                ->first();
            if (is_null($getPelayanan)) :
                throw new \Exception($this->outputMessage('not found', 'pelayanan'));
            endif;

            /**
             * get LSPOP
             */
            $getLspop = $this->checkerHelpers->lspopChecker(['nomor_pelayanan' => $getPelayanan->nomor_pelayanan]);

            /**
             * status kolektif
             */
            $keteranganStatus = $getPelayanan->status_kolektif == 0 ? "Individu" : ($getPelayanan->status_kolektif == 1 ? "Fasilitas Umum" : "Masal / Kolektif");

            /**
             * get region
             */
            $getProvinsi    = $this->checkerHelpers->provinsiChecker(['id_provinsi' => $getPelayanan->id_provinsi]);
            $getKabupaten   = $this->checkerHelpers->kabupatenChecker(['id_kabupaten' => $getPelayanan->id_kabupaten]);
            $getKecamatan   = $this->checkerHelpers->kecamatanChecker(['id_kecamatan' => $getPelayanan->id_kecamatan]);
            $getKelurahan   = $this->checkerHelpers->kelurahanChecker(['id_kelurahan' => $getPelayanan->id_kelurahan]);
            $getProvinsiSp    = $this->checkerHelpers->provinsiChecker(['id_provinsi' => $getPelayanan->sp_id_provinsi]);
            $getKabupatenSp   = $this->checkerHelpers->kabupatenChecker(['id_kabupaten' => $getPelayanan->sp_id_kabupaten]);
            $getKecamatanSp   = $this->checkerHelpers->kecamatanChecker(['id_kecamatan' => $getPelayanan->sp_id_kecamatan]);
            $getKelurahanSp   = $this->checkerHelpers->kelurahanChecker(['id_kelurahan' => $getPelayanan->sp_id_kelurahan]);
            $getOpKecamatan = $this->checkerHelpers->refrensiKecamatanChecker(['KD_KECAMATAN' => $getPelayanan->op_kd_kecamatan]);
            $getOpKelurahan = $this->checkerHelpers->refrensiKelurahanChecker(['KD_KECAMATAN' => $getPelayanan->op_kd_kecamatan, 'KD_KELURAHAN' => $getPelayanan->op_kd_kelurahan]);

            /**
             * get pekerjaan
             */
            $getPekerjaan = $this->checkerHelpers->pekerjaanChecker(['kode' => $getPelayanan->sp_kd_pekerjaan]);

            /**
             * status verifikasi & nop
             */
            $statusVerifikasi = $getPelayanan->status_verifikasi == 1 ? "Permohonan Baru" : ($getPelayanan->status_verifikasi == 2 ? "Ditolak" : ($getPelayanan->status_verifikasi == 3 ? "Diverifikasi Kasubbid" : "Ditetapkan Kabid"));
            $nop = $getPelayanan->status_verifikasi == 4 ? $this->nop($getPelayanan->op_kd_kecamatan, $getPelayanan->op_kd_kelurahan, $getPelayanan->op_kd_blok, $getPelayanan->no_urut, $getPelayanan->status_kolektif) : null;

            /**
             * format tanggal pendaftaran
             */
            $tanggalPendaftaran = Carbon::parse($getPelayanan->created_at)->locale('id');
            $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);

            $data = [
                "spop" => [
                    'nomor_pelayanan' => $getPelayanan->nomor_pelayanan,
                    'layanan'         => $getPelayanan->layanan,
                    'status_kolektif' => [
                        'kode' => $getPelayanan->status_kolektif,
                        'nama' => $keteranganStatus
                    ],
                    'jenis_pelayanan' => $getPelayanan->jenis_layanan,
                    'id_pemohon'      => $getPelayanan->id_pemohon,
                    'nama_lengkap'    => $getPelayanan->nama_lengkap,
                    'provinsi'        => [
                        'id' => $getProvinsi->id_provinsi,
                        'nama' => $getProvinsi->nama_provinsi
                    ],
                    'kabupaten'       => [
                        'id' => $getKabupaten->id_kabupaten,
                        'nama' => $getKabupaten->nama_kabupaten
                    ],
                    'kecamatan'       => [
                        'id' => $getKecamatan->id_kecamatan,
                        'nama' => $getKecamatan->nama_kecamatan
                    ],
                    'kelurahan'       => [
                        'id' => $getKelurahan->id_kelurahan,
                        'nama' => $getKelurahan->nama_kelurahan
                    ],
                    'alamat'          => $getPelayanan->alamat
                ],
                "data_subjek_pajak" => [
                    'nama_lengkap' => $getPelayanan->sp_nama_lengkap,
                    'alamat'       => $getPelayanan->sp_alamat,
                    'rt'           => $getPelayanan->sp_rt,
                    'rw'           => $getPelayanan->sp_rw,
                    'no_hp'        => $getPelayanan->sp_no_hp,
                    'npwp'         => $getPelayanan->sp_npwp,
                    'nik'          => $getPelayanan->sp_nik,
                    'pekerjaan' => [
                        'kode' => $getPelayanan->sp_kd_pekerjaan,
                        'nama' => $getPekerjaan->nama
                    ],
                    'provinsi'        => [
                        'id' => $getProvinsiSp->id_provinsi,
                        'nama' => $getProvinsiSp->nama_provinsi
                    ],
                    'kabupaten'       => [
                        'id' => $getKabupatenSp->id_kabupaten,
                        'nama' => $getKabupatenSp->nama_kabupaten
                    ],
                    'kecamatan'       => [
                        'id' => $getKecamatanSp->id_kecamatan,
                        'nama' => $getKecamatanSp->nama_kecamatan
                    ],
                    'kelurahan'       => [
                        'id' => $getKelurahanSp->id_kelurahan,
                        'nama' => $getKelurahanSp->nama_kelurahan
                    ],
                ],
                "data_objek_pajak"  => [
                    'provinsi'              => $this->provinsi[1],
                    'kabupaten'             => $this->kabupaten[1],
                    'kecamatan'             => [
                        'kode' => $getOpKecamatan->KD_KECAMATAN,
                        'nama' => $getOpKecamatan->NM_KECAMATAN
                    ],
                    'kelurahan'             => [
                        'kode' => $getOpKelurahan->KD_KELURAHAN,
                        'nama' => $getOpKelurahan->NM_KELURAHAN
                    ],
                    'blok'                  => $getPelayanan->op_kd_blok,
                    'kelas_bumi'            => $getPelayanan->op_kelas_bumi,
                    'jenis_tanah'           => $getPelayanan->op_jenis_tanah,
                    'luas_tanah'            => $getPelayanan->op_luas_tanah,
                    'luas_bangunan'         => $getPelayanan->op_luas_bangunan,
                    'alamat'                => $getPelayanan->alamat,
                    'fc_surat_tanah'        => $getPelayanan->fc_surat_tanah,
                    'ktp_pemilik'           => $getPelayanan->ktp_pemilik,
                    'sppt_tetangga_sebelah' => $getPelayanan->sppt_tetangga_sebelah,
                    'foto_objek_pajak'      => $getPelayanan->foto_objek_pajak,
                    'spop'                  => $getPelayanan->spop,
                    'lspop'                 => $getPelayanan->lspop,
                ],
                "lspop"               => $getLspop,
                'no_urut'             => is_null($getPelayanan->no_urut) ? '-' : $getPelayanan->no_urut,
                'status_verifikasi'   => $statusVerifikasi,
                'tanggal_pendaftaran' => $tanggalPendaftaran->format('l, j F Y ; h:i:s '),
                'created_by'          => $getPelayanan->created_by,
                'uuid_pelayanan'      => $getPelayanan->uuid_pelayanan,
                'nop'                 => $nop
            ];

            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
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
             * jika status verifikasi 2
             */
            if ($request['status_verifikasi'] == 2) :
                $request['uuid_user_reject'] = $this->idUser;
                $request['role_reject'] = $this->roleUser;
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
             * jika status verifikasi 3
             */
            // if ($request['status_verifikasi'] == 3 && $getSetting->description == 'enabled') :

            //     /**
            //      * get data kabid
            //      */
            //     $getKabid = $this->checkerHelpers->userChecker(['role' => 'kabid']);
            //     if (is_null($getKabid)) :
            //         throw new \Exception($this->outputMessage('not found', 'kabid'));
            //     endif;

            //     /**
            //      * get tanggal pendaftaran
            //      */
            //     $tanggalPendaftaran = Carbon::parse($getPelayanan->created_at)->locale('id');
            //     $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);

            //     /**
            //      * send whatsapp notification
            //      */
            //     $message = "Permohonan Pendaftaran Baru";
            //     $message .= "\nAtas nama " . $getPelayanan->nama_lengkap;
            //     $message .= "\nNo Pelayanan " . $getPelayanan->nomor_pelayanan;
            //     $message .= "\nData telah diverifikasi";
            //     $message .= "\n" . $tanggalPendaftaran->format('l, j F Y ; h:i:s ') . "\n\n";
            //     $message .= "\nMohon segera diproses";
            //     $message .= "\n(KASUBBID)";
            //     $message .= "\n _BPKPAD KOTA BINJAI_";
            //     $callBack = $this->whatsapp($getKabid->no_hp, $message);

            //     /**
            //      * fail send whatsapp notification
            //      */
            //     if ($callBack->status != true) :
            //         throw new \Exception($this->outputMessage('unsend', 'whatsapp'));
            //     endif;
            // endif;

            /**
             * jika satatus verifikasi 4
             */
            if (
                $request['status_verifikasi'] == 4
                /** && $getSetting->description == 'enabled' **/
            ) :

                /**
                 * get keluarahan
                 */
                $getKelurahan   = $this->checkerHelpers->kelurahanChecker(["id_kelurahan" => $getPelayanan->id_kelurahan]);
                if (is_null($getKelurahan)) :
                    throw new \Exception($this->outputMessage('not found', 'kelurahan'));
                endif;

                /**
                 * get kota
                 */
                $getKota        = $this->checkerHelpers->kabupatenChecker(["id_kabupaten" => $getPelayanan->id_kabupaten]);
                if (is_null($getKota)) :
                    throw new \Exception($this->outputMessage('not found', 'kota'));
                endif;

                $kdProvinsi     = $this->provinsi[0];
                $kdKabupaten    = $this->kabupaten[0];
                $kdKecamatan    = $getPelayanan->op_kd_kecamatan;
                $kdKelurahan    = $getPelayanan->op_kd_kelurahan;
                $kdBlok         = $getPelayanan->op_kd_blok;
                $noUrut         = $getPelayanan->no_urut;
                $kdJenisOp      = $getPelayanan->status_kolektif;
                $spopKelurahan  = $getKelurahan->nama_kelurahan;
                $spopKota       = $getKota->nama_kabupaten;

                /**
                 * get lspop
                 */
                $sqlLspop = $this->lspop->where(['nomor_pelayanan' => $getPelayanan->nomor_pelayanan]);
                $getLspop = $sqlLspop->get();

                /**
                 * perhitungan
                 */
                $luasBumi             = $getPelayanan->op_luas_tanah;
                $njopBangunan         = 0;
                $njopBangunanPermeter = 0;
                $totalLuasBangunan    = 0;
                $njoptkpSppt          = $this->njoptkp(count($getLspop));
                $faktorPengurang      = $this->faktorPengurang();
                $getKelasBumi         = $this->checkerHelpers->kelasBumiChecker(['KD_KLS_TANAH' => $getPelayanan->op_kelas_bumi]);
                $njopBumi             = $this->njopBumi($getKelasBumi->NILAI_PER_M2_TANAH, $luasBumi);
                $njopSppt             = $this->njopSppt($njopBumi, $njopBangunan);
                $pbbTerhutang         = $this->pbbTerhutang($njopSppt, $njoptkpSppt);
                $pbbHarusDibayar      = $this->pbbHarusDibayar($pbbTerhutang, $faktorPengurang);
                $kdKelasBangunan      = 'XXX';
                $thnAwalKelasBangunan = '1986';

                /**
                 * lspop
                 */
                $datOpBangunanInput = [];
                if (!is_null($sqlLspop->first())) :
                    $njoptkpSppt = 10000000;
                    foreach ($getLspop as $key => $value) :
                        $totalLuasBangunan += $value->luas_bangunan;
                        $valueCalculateLspop = [
                            'atap'              => $value->atap,
                            'dinding'           => $value->dinding,
                            'lantai'            => $value->lantai,
                            'langit_langit'     => $value->langit_langit,
                            'kd_jpb'            => $value->jenis_bangunan,
                            'thn_dbkb_standard' => $value->thn_dibangun,
                            'tipe_bng'          => $value->luas_bangunan,
                            'kd_bng_lantai'     => $value->jlh_lantai,
                            'thn_renovasi'      => $value->thn_renovasi,
                            'kondisi_bangunan'  => $value->kondisi_bangunan,
                            'kd_kecamatan'      => $kdKecamatan,
                            'kd_kelurahan'      => $kdKelurahan,
                            'kd_blok'           => $kdBlok,
                            'no_urut'           => $noUrut,
                            'kd_jns_op'         => $kdJenisOp
                        ];
                        $calculateNjopBng = round($this->hitungLspop($valueCalculateLspop)['njop']);
                        $calculateNjopBngPerMeter = round($this->hitungLspop($valueCalculateLspop)['per_meter']);
                        $njopBangunan += $calculateNjopBng;
                        $njopBangunanPermeter += $calculateNjopBngPerMeter;

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
                            'NO_BNG'              => count($getLspop),
                            'KD_JPB'              => $value->jenis_bangunan,
                            'NO_FORMULIR_LSPOP'   => $value->nomor_pelayanan,
                            'THN_DIBANGUN_BNG'    => $value->thn_dibangun,
                            'THN_RENOVASI_BNG'    => $value->thn_renovasi,
                            'LUAS_BNG'            => $totalLuasBangunan,
                            'JML_LANTAI_BNG'      => $value->jlh_lantai,
                            'KONDISI_BNG'         => $value->kondisi_bangunan,
                            'JNS_KONSTRUKSI_BNG'  => $value->konstruksi,
                            'JNS_ATAP_BNG'        => $value->atap,
                            'KD_DINDING'          => $value->dinding,
                            'KD_LANTAI'           => $value->lantai,
                            'KD_LANGIT_LANGIT'    => $value->langit_langit,
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
                 * get kelas bangunan
                 */
                $getKelasBangunan = $this->checkerHelpers->kelasBangunanChecker($njopBangunanPermeter);
                if (!is_null($getKelasBangunan)) :
                    $kdKelasBangunan      = $getKelasBangunan->KD_KLS_BNG;
                    $thnAwalKelasBangunan = $getKelasBangunan->THN_AWAL_KLS_BNG;
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
                $spptInput['NM_WP_SPPT']                = $getPelayanan->sp_nama_lengkap;
                $spptInput['JLN_WP_SPPT']               = $getPelayanan->sp_alamat;
                $spptInput['BLOK_KAV_NO_WP_SPPT']       = $getPelayanan->sp_blok;
                $spptInput['RW_WP_SPPT']                = $getPelayanan->sp_rw;
                $spptInput['RT_WP_SPPT']                = $getPelayanan->sp_rt;
                $spptInput['KELURAHAN_WP_SPPT']         = $spopKelurahan;
                $spptInput['KOTA_WP_SPPT']              = $spopKota;
                $spptInput['KD_POS_WP_SPPT']            = $getPelayanan->sp_kd_pos;
                $spptInput['NPWP_SPPT']                 = $getPelayanan->sp_npwp;
                $spptInput['NO_PERSIL_SPPT']            = null;
                $spptInput['KD_KLS_TANAH']              = $getPelayanan->op_kelas_bumi;
                $spptInput['THN_AWAL_KLS_TANAH']        = $getKelasBumi->THN_AWAL_KLS_TANAH;
                $spptInput['KD_KLS_BNG']                = $kdKelasBangunan;
                $spptInput['THN_AWAL_KLS_BNG']          = $thnAwalKelasBangunan;
                $spptInput['TGL_JATUH_TEMPO_SPPT']      = $this->year . '-09-30';
                $spptInput['LUAS_BUMI_SPPT']            = $luasBumi;
                $spptInput['LUAS_BNG_SPPT']             = $totalLuasBangunan;
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
                 * input for subjek pajak
                 */
                $subjekPajakInput['SUBJEK_PAJAK_ID']     = $getPelayanan->sp_nik;
                $subjekPajakInput['NM_WP']               = $getPelayanan->sp_nama_lengkap;
                $subjekPajakInput['JALAN_WP']            = $getPelayanan->sp_alamat;
                $subjekPajakInput['BLOK_KAV_NO_WP']      = $getPelayanan->sp_blok;
                $subjekPajakInput['RW_WP']               = $getPelayanan->sp_rw;
                $subjekPajakInput['RT_WP']               = $getPelayanan->sp_rt;
                $subjekPajakInput['KELURAHAN_WP']        = $spopKelurahan;
                $subjekPajakInput['KOTA_WP']             = $spopKota;
                $subjekPajakInput['KD_POS_WP']           = $getPelayanan->sp_kd_pos;
                $subjekPajakInput['TELP_WP']             = $getPelayanan->sp_no_hp;
                $subjekPajakInput['NPWP']                = $getPelayanan->sp_npwp;
                $subjekPajakInput['STATUS_PEKERJAAN_WP'] = $getPelayanan->sp_kd_pekerjaan;

                /**
                 * input for objek pajak
                 */
                $objekPajakInput['KD_PROPINSI']         = $kdProvinsi;
                $objekPajakInput['KD_DATI2']            = $kdKabupaten;
                $objekPajakInput['KD_KECAMATAN']        = $kdKecamatan;
                $objekPajakInput['KD_KELURAHAN']        = $kdKelurahan;
                $objekPajakInput['KD_BLOK']             = $kdBlok;
                $objekPajakInput['NO_URUT']             = $noUrut;
                $objekPajakInput['KD_JNS_OP']           = $kdJenisOp;
                $objekPajakInput['SUBJEK_PAJAK_ID']     = $getPelayanan->id_pemohon;
                $objekPajakInput['NO_FORMULIR_SPOP']    = $getPelayanan->nomor_pelayanan;
                $objekPajakInput['NO_PERSIL']           = null;
                $objekPajakInput['JALAN_OP']            = $getPelayanan->op_alamat;
                $objekPajakInput['BLOK_KAV_NO_OP']      = $getPelayanan->op_blok;
                $objekPajakInput['RW_OP']               = $getPelayanan->op_rw;
                $objekPajakInput['RT_OP']               = $getPelayanan->op_rt;
                $objekPajakInput['KD_STATUS_CABANG']    = 0;
                $objekPajakInput['TOTAL_LUAS_BUMI']     = $getPelayanan->op_luas_tanah;
                $objekPajakInput['TOTAL_LUAS_BNG']      = $totalLuasBangunan;
                $objekPajakInput['NJOP_BUMI']           = $njopBumi;
                $objekPajakInput['NJOP_BNG']            = $njopBangunan;
                $objekPajakInput['NIP_PENDATA']         = authAttribute()['nip'];
                $objekPajakInput['TGL_PENDATAAN_OP']    = $this->datetime;
                $objekPajakInput['NIP_PEMERIKSA_OP']    = authAttribute()['nip'];
                $objekPajakInput['TGL_PEMERIKSAAN_OP']  = $this->datetime;
                $objekPajakInput['NIP_PEREKAM_OP']      = authAttribute()['nip'];

                /**
                 * input for objek bumi
                 */
                $opBumiInput['KD_PROPINSI']        = $kdProvinsi;
                $opBumiInput['KD_DATI2']           = $kdKabupaten;
                $opBumiInput['KD_KECAMATAN']       = $kdKecamatan;
                $opBumiInput['KD_KELURAHAN']       = $kdKelurahan;
                $opBumiInput['KD_BLOK']            = $kdBlok;
                $opBumiInput['NO_URUT']            = $noUrut;
                $opBumiInput['KD_JNS_OP']          = $kdJenisOp;
                $opBumiInput['KD_ZNT']             = $getPelayanan->kd_znt;
                $opBumiInput['LUAS_BUMI']          = $luasBumi;
                $opBumiInput['JNS_BUMI']           = $getPelayanan->op_jenis_tanah;
                $opBumiInput['NILAI_SISTEM_BUMI']  = $njopBumi;

                /**
                 * save sppt
                 */
                $saveSppt = $this->sppt->insert($spptInput);
                if (!$saveSppt) :
                    throw new \Exception($this->outputMessage('unsaved', 'SPPT'));
                endif;

                /**
                 * save subjek pajak
                 */
                $saveSubjekPajak = $this->datSubjekPajak->insert($subjekPajakInput);
                if (!$saveSubjekPajak) :
                    throw new \Exception($this->outputMessage('unsaved', 'Subjek Pajak'));
                endif;

                /**
                 * save objek pajak
                 */
                $saveObjekPajak = $this->datObjekPajak->insert($objekPajakInput);
                if (!$saveObjekPajak) :
                    throw new \Exception($this->outputMessage('unsaved', 'Objek Pajak'));
                endif;

                /**
                 * save op bumi
                 */
                $saveOpBumi = $this->datOpBumi->insert($opBumiInput);
                if (!$saveOpBumi) :
                    throw new \Exception($this->outputMessage('unsaved', 'Objek Bumi'));
                endif;

            // /**
            //  * send whatsapp notification
            //  */
            // if ($getSetting->description == 'enabled') :
            //     $message = "Yth " . $namaWpSppt;
            //     $message .= "\nNOP anda adalah " . $nop;
            //     $message .= "\nTerima Kasih telah mendaftarkan objek PBB anda\n\n";
            //     $message .= "\nTTD";
            //     $message .= "\nKABID & BPHTB";
            //     $message .= "\nBPKPAD KOTA BINJAI";
            //     $callBack = $this->whatsapp($target, $message);

            //     /**
            //      * fail send whatsapp notification
            //      */
            //     if ($callBack->status != true) :
            //         throw new \Exception($this->outputMessage('unsend', 'whatsapp'));
            //     endif;
            // endif;

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
     * delete data from db
     */
    public function delete($uuidPelayanan, $uuidUser)
    {
        DB::beginTransaction();
        try {
            /**
             * is data valid
             */
            $getData = $this->checkerHelpers->pelayananChecker(["uuid_pelayanan" => $uuidPelayanan]);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'pelayanan'));
            endif;

            /**
             * is user kasubbid
             */
            $getUser = $this->checkerHelpers->userChecker(["uuid_user" => $uuidUser]);
            if (is_null($getUser)) :
                throw new \Exception($this->outputMessage('not found', 'pengguna'));
            endif;
            if ($getUser->role != 'kasubbid' || $getUser->role != 'superadmin' || $getUser->role != 'admin') :
                throw new \Exception($this->outputMessage('resitrict', 'kasubbid'));
            endif;

            /**
             * fc surat tanah
             */
            if (!is_null($getData->fc_surat_tanah)) :
                if (file_exists($this->storage . "/" . $getData->fc_surat_tanah)) :
                    if (!unlink($this->storage . "/" . $getData->fc_surat_tanah)) :
                        throw new \Exception($this->outputMessage('remove fail', $getData->fc_surat_tanah));
                    endif;
                endif;
            endif;

            /**
             * ktp pemilik
             */
            if (!is_null($getData->ktp_pemilik)) :
                if (file_exists($this->storage . "/" . $getData->ktp_pemilik)) :
                    if (!unlink($this->storage . "/" . $getData->ktp_pemilik)) :
                        throw new \Exception($this->outputMessage('remove fail', $getData->ktp_pemilik));
                    endif;
                endif;
            endif;

            /**
             * sppt tetangga sebelah
             */
            if (!is_null($getData->sppt_tetangga_sebelah)) :
                if (file_exists($this->storage . "/" . $getData->sppt_tetangga_sebelah)) :
                    if (!unlink($this->storage . "/" . $getData->sppt_tetangga_sebelah)) :
                        throw new \Exception($this->outputMessage('remove fail', $getData->sppt_tetangga_sebelah));
                    endif;
                endif;
            endif;

            /**
             * foto objek pajak
             */
            if (!is_null($getData->foto_objek_pajak)) :
                if (file_exists($this->storage . "/" . $getData->foto_objek_pajak)) :
                    if (!unlink($this->storage . "/" . $getData->foto_objek_pajak)) :
                        throw new \Exception($this->outputMessage('remove fail', $getData->foto_objek_pajak));
                    endif;
                endif;
            endif;

            /**
             * delete data
             */
            $delete = $this->pelayanan->where('uuid_pelayanan', $uuidPelayanan)->delete();
            if (!$delete) :
                throw new \Exception($this->outputMessage('undeleted', 'pelayanan'));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('deleted', 'pelayanan'));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * update data to db
     */
    public function update($uuidPelayanan, $request)
    {
        $request = collect($request)->except(['_method'])->toArray();
        DB::beginTransaction();
        try {

            /**
             * check if data exist
             */
            $getPelayanan             = $this->checkerHelpers->pelayananChecker(["uuid_pelayanan" => $uuidPelayanan]);
            $fcSuratTanah             = isset($getPelayanan->fc_surat_tanah) ? $getPelayanan->fc_surat_tanah : null;
            $ktpPemilikTanah          = isset($getPelayanan->ktp_pemilik) ? $getPelayanan->ktp_pemilik : null;
            $spptTetanggaSebelahTanah = isset($getPelayanan->sppt_tetangga_sebelah) ? $getPelayanan->sppt_tetangga_sebelah : null;
            $fotoObjekPajakTanah      = isset($getPelayanan->foto_objek_pajak) ? $getPelayanan->foto_objek_pajak : null;
            $spop                     = isset($getPelayanan->spop) ? $getPelayanan->spop : null;
            $lspop                    = isset($getPelayanan->lspop) ? $getPelayanan->lspop : null;

            if (is_null($getPelayanan)) :
                throw new \Exception($this->outputMessage('not found', 'pelayanan'));
            endif;

            /**
             * if fc surat tanah exist
             */
            if (isset($_FILES['fc_surat_tanah'])) :

                /**
                 * remove file
                 */
                if (!is_null($fcSuratTanah) && file_exists($this->storage . "/" . $fcSuratTanah)) :
                    unlink($this->storage . "/" . $fcSuratTanah);
                endif;

                /**
                 * upload file
                 */
                $fcSuratTanahName        = $_FILES['fc_surat_tanah']['name'];
                $fcSuratTanahTempName    = $_FILES['fc_surat_tanah']['tmp_name'];
                $fcSuratTanahExt         = explode('.', $fcSuratTanahName);
                $fcSuratTanahActualExt   = strtolower(end($fcSuratTanahExt));
                $fcSuratTanahNew         = Uuid::uuid4()->getHex() . "." . $fcSuratTanahActualExt;
                $fcSuratTanahDestination = $this->storage . '/' . $fcSuratTanahNew;
                if (!move_uploaded_file($fcSuratTanahTempName, $fcSuratTanahDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['fc_surat_tanah'] = $fcSuratTanahNew;
            endif;

            /**
             * if ktp pemilik exist
             */
            if (isset($_FILES['ktp_pemilik'])) :

                /**
                 * remove file
                 */
                if (!is_null($ktpPemilikTanah) && file_exists($this->storage . "/" . $ktpPemilikTanah)) :
                    unlink($this->storage . "/" . $ktpPemilikTanah);
                endif;

                /**
                 * upload file
                 */
                $ktpPemilikTanahName        = $_FILES['ktp_pemilik']['name'];
                $ktpPemilikTanahTempName    = $_FILES['ktp_pemilik']['tmp_name'];
                $ktpPemilikTanahExt         = explode('.', $ktpPemilikTanahName);
                $ktpPemilikTanahActualExt   = strtolower(end($ktpPemilikTanahExt));
                $ktpPemilikTanahNew         = Uuid::uuid4()->getHex() . "." . $ktpPemilikTanahActualExt;
                $ktpPemilikTanahDestination = $this->storage . '/' . $ktpPemilikTanahNew;
                if (!move_uploaded_file($ktpPemilikTanahTempName, $ktpPemilikTanahDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['ktp_pemilik'] = $ktpPemilikTanahNew;
            endif;

            /**
             * if sppt tetangga sebelah exist
             */
            if (isset($_FILES['sppt_tetangga_sebelah'])) :

                /**
                 * remove file
                 */
                if (!is_null($spptTetanggaSebelahTanah) && file_exists($this->storage . "/" . $spptTetanggaSebelahTanah)) :
                    unlink($this->storage . "/" . $spptTetanggaSebelahTanah);
                endif;

                /**
                 * upload file
                 */
                $spptTetanggaSebelahTanahName        = $_FILES['sppt_tetangga_sebelah']['name'];
                $spptTetanggaSebelahTanahTempName    = $_FILES['sppt_tetangga_sebelah']['tmp_name'];
                $spptTetanggaSebelahTanahExt         = explode('.', $spptTetanggaSebelahTanahName);
                $spptTetanggaSebelahTanahActualExt   = strtolower(end($spptTetanggaSebelahTanahExt));
                $spptTetanggaSebelahTanahNew         = Uuid::uuid4()->getHex() . "." . $spptTetanggaSebelahTanahActualExt;
                $spptTetanggaSebelahTanahDestination = $this->storage . '/' . $spptTetanggaSebelahTanahNew;
                if (!move_uploaded_file($spptTetanggaSebelahTanahTempName, $spptTetanggaSebelahTanahDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['sppt_tetangga_sebelah'] = $spptTetanggaSebelahTanahNew;
            endif;

            /**
             * if foto obkek pajak exist
             */
            if (isset($_FILES['foto_objek_pajak'])) :

                /**
                 * remove file
                 */
                if (!is_null($fotoObjekPajakTanah) && file_exists($this->storage . "/" . $fotoObjekPajakTanah)) :
                    unlink($this->storage . "/" . $fotoObjekPajakTanah);
                endif;

                /**
                 * upload file
                 */
                $fotoObjekPajakTanahName        = $_FILES['foto_objek_pajak']['name'];
                $fotoObjekPajakTanahTempName    = $_FILES['foto_objek_pajak']['tmp_name'];
                $fotoObjekPajakTanahExt         = explode('.', $fotoObjekPajakTanahName);
                $fotoObjekPajakTanahActualExt   = strtolower(end($fotoObjekPajakTanahExt));
                $fotoObjekPajakTanahNew         = Uuid::uuid4()->getHex() . "." . $fotoObjekPajakTanahActualExt;
                $fotoObjekPajakTanahDestination = $this->storage . '/' . $fotoObjekPajakTanahNew;
                if (!move_uploaded_file($fotoObjekPajakTanahTempName, $fotoObjekPajakTanahDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['foto_objek_pajak'] = $fotoObjekPajakTanahNew;
            endif;

            /**
             * if spop exist
             */
            if (isset($_FILES['spop'])) :

                /**
                 * remove file
                 */
                if (!is_null($spop) && file_exists($this->storage . "/" . $spop)) :
                    unlink($this->storage . "/" . $spop);
                endif;

                /**
                 * upload file
                 */
                $spopName        = $_FILES['spop']['name'];
                $spopTempName    = $_FILES['spop']['tmp_name'];
                $spopExt         = explode('.', $spopName);
                $spopActualExt   = strtolower(end($spopExt));
                $spopNew         = Uuid::uuid4()->getHex() . "." . $spopActualExt;
                $spopDestination = $this->storage . '/' . $spopNew;
                if (!move_uploaded_file($spopTempName, $spopDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['spop'] = $spopNew;
            endif;

            /**
             * if lspop exist
             */
            if (isset($_FILES['lspop'])) :

                /**
                 * remove file
                 */
                if (!is_null($lspop) && file_exists($this->storage . "/" . $lspop)) :
                    unlink($this->storage . "/" . $lspop);
                endif;

                /**
                 * upload file
                 */
                $lspopName        = $_FILES['lspop']['name'];
                $lspopTempName    = $_FILES['lspop']['tmp_name'];
                $lspopExt         = explode('.', $lspopName);
                $lspopActualExt   = strtolower(end($lspopExt));
                $lspopNew         = Uuid::uuid4()->getHex() . "." . $lspopActualExt;
                $lspopDestination = $this->storage . '/' . $lspopNew;
                if (!move_uploaded_file($lspopTempName, $lspopDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['lspop'] = $lspopNew;
            endif;

            /**
             * update data
             */
            $request['updated_by'] = authAttribute()['id'];
            $request['no_urut'] = $this->noUrutSppt($request['op_kd_kecamatan'], $request['op_kd_kelurahan'], $request['op_kd_blok']);
            $request['status_verifikasi'] = $getPelayanan->status_verifikasi;
            $updateData = $this->pelayanan->where(['uuid_pelayanan' => $uuidPelayanan])->update($request);
            if (!$updateData) :
                throw new \Exception($this->outputMessage('update fail', 'permohonan data baru dengan nomor pelayanan ' . $getPelayanan->nomor_pelayanan));
            else :
                DB::commit();
                $response = $this->success($this->outputMessage('updated', 'permohonan data baru dengan nomor pelayanan ' . $getPelayanan->nomor_pelayanan));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }
}

<?php

namespace App\Repositories\Pelayanan\Pbb\PetaObjekPajak;

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

/**
 * import models
 */

use App\Models\Pelayanan\PelayananBphtb\PelayananBphtb;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\Pelayanan\RiwayatDitolakBphtb\RiwayatDitolakBphtb;
use App\Models\Sppt\Sppt;
use App\Models\Pelayanan\Lspop\Lspop;
use App\Models\Pelayanan\PetaObjekPajak\PetaObjekPajak;
use App\Models\Pelayanan\StatusNop\StatusNop;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Pbb\PetaObjekPajak\PetaObjekPajakRepositories;

class EloquentPetaObjekPajakRepositories implements PetaObjekPajakRepositories
{
    use Message, Response, Notification;

    private $lspop;
    private $sppt;
    private $storage;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $year;
    private $petaObjekPajak;
    private $statusNop;
    private $pelayananBphtb;
    private $datObjekPajak;
    private $datSubjekPajak;
    private $riwayatDitolakBphtb;

    public function __construct(
        Sppt $sppt,
        CheckerHelpers $checkerHelpers,
        Lspop $lspop,
        PaginateHelpers $paginateHelpers,
        PetaObjekPajak $petaObjekPajak,
        StatusNop $statusNop,
        PelayananBphtb $pelayananBphtb,
        DatObjekPajak $datObjekPajak,
        RiwayatDitolakBphtb $riwayatDitolakBphtb,
    ) {
        /**
         * initialize model
         */
        $this->pelayananBphtb = $pelayananBphtb;
        $this->sppt = $sppt;
        $this->lspop = $lspop;
        $this->paginateHelpers = $paginateHelpers;
        $this->petaObjekPajak = $petaObjekPajak;
        $this->statusNop = $statusNop;
        $this->datObjekPajak = $datObjekPajak;
        $this->riwayatDitolakBphtb = $riwayatDitolakBphtb;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->storage   = path('peta op');
        $this->provinsi  = [globalAttribute()['kdProvinsi'], 'SUMATERA UTARA'];
        $this->kabupaten = [globalAttribute()['kdKota'], 'BINJAI'];
        $this->year      = Carbon::now()->format('Y');
    }

    /**
     * store data to db
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {

            /**
             * save data peta objek pajak
             */
            $inputPetaOp = [
                'nop' => $request['nop'],
                'nama' => $request['nama'],
                'tahun' => $request['tahun'],
                'alamat' => $request['alamat'],
            ];

            $photoBatasTanah = [];
            $koordinat = [];
            if (isset($_FILES['photo_batas_tanah'])) :
                foreach ($request['photo_batas_tanah'] as $key => $item) :

                    /**
                     * photo batas tanah
                     */
                    $fotoBatasTanahName        = $_FILES['photo_batas_tanah']['name'][$key];
                    $fotoBatasTanahTempName    = $_FILES['photo_batas_tanah']['tmp_name'][$key];
                    $fotoBatasTanahExt         = explode('.', $fotoBatasTanahName);
                    $fotoBatasTanahActualExt   = strtolower(end($fotoBatasTanahExt));
                    $fotoBatasTanahNew         = Uuid::uuid4()->getHex() . "." . $fotoBatasTanahActualExt;
                    $fotoBatasTanahDestination = $this->storage . '/' . $fotoBatasTanahNew;
                    if (!move_uploaded_file($fotoBatasTanahTempName, $fotoBatasTanahDestination)) :
                        throw new \Exception($this->outputMessage('directory'));
                    endif;
                    array_push($photoBatasTanah, $fotoBatasTanahNew);

                    /**
                     * koordinat
                     */
                    $setKoordinat = [
                        'latitude' => $request['latitude_batas_tanah'][$key],
                        'longitude' => $request['longitude_batas_tanah'][$key]
                    ];
                    array_push($koordinat, $setKoordinat);
                endforeach;
            endif;
            $inputPetaOp['photo'] = json_encode($photoBatasTanah);
            $inputPetaOp['koordinat'] = json_encode($koordinat);
            $savePetaOp = $this->petaObjekPajak->create($inputPetaOp);
            if (!$savePetaOp) :
                throw new \Exception($this->outputMessage('unsaved', 'peta objek pajak ' . $request['nama']));
            endif;

            /**
             * save kategori nop
             */
            $inputKategoriNop = [
                'nop' => $request['nop'],
                'nama' => $request['nama'],
                'kategori_nop' => $request['kategori_nop'],
            ];
            $saveKategoriOp = $this->statusNop->create($inputKategoriNop);
            if (!$saveKategoriOp) :
                throw new \Exception($this->outputMessage('unsaved', 'kategori NOP ' . $request['nama']));
            endif;

            /**
             * update SPPT
             */
            $inputSppt = [
                'latitude' => $request['latitude_depan'],
                'longitude' => $request['longitude_depan'],
            ];
            if (isset($_FILES['photo_depan'])) :
                $photoDepanName        = $_FILES['photo_depan']['name'];
                $photoDepanTempName    = $_FILES['photo_depan']['tmp_name'];
                $photoDepanExt         = explode('.', $photoDepanName);
                $photoDepanActualExt   = strtolower(end($photoDepanExt));
                $photoDepanNew         = Uuid::uuid4()->getHex() . "." . $photoDepanActualExt;
                $photoDepanDestination = $this->storage . '/' . $photoDepanNew;
                if (!move_uploaded_file($photoDepanTempName, $photoDepanDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $inputSppt['photo'] = $photoDepanNew;
            endif;
            $updateSppt = $this->sppt->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = ' . $request['nop'])->update($inputSppt);
            if (!$updateSppt) :
                throw new \Exception($this->outputMessage('update fail', 'SPPT ' . $request['nop']));
            endif;

            /**
             * update SPPT
             */
            $inpuLspop = [
                'kondisi_umum' => $request['kondisi_umum'],
                'md_dalam_jenis' => $request['md_dalam_jenis'],
                'md_dalam' => $request['md_dalam'],
                'md_luar' => $request['md_luar'],
                'md_luar_jml_lt' => $request['md_luar_jml_lt'],
                'pd_dalam_jenis' => $request['pd_dalam_jenis'],
                'pd_dalam_jml_lt' => $request['pd_dalam_jml_lt'],
                'pd_dalam' => $request['pd_dalam'],
                'pd_luar_jenis' => $request['pd_luar_jenis'],
                'pd_luar_jml_lt' => $request['pd_luar_jml_lt'],
                'pd_luar' => $request['pd_luar'],
                'langit_langit_jenis' => $request['langit_langit_jenis'],
                'langit_langit_jml_lt' => $request['langit_langit_jml_lt'],
                'langit_langit' => $request['langit_langit'],
                'atap' => $request['atap'],
                'penutup_lantai_jenis' => $request['penutup_lantai_jenis'],
                'penutup_lantai_jml_lt' => $request['penutup_lantai_jml_lt'],
                'penutup_lantai' => $request['penutup_lantai'],
            ];
            $getNomorLayanan = $this->lspop->select(
                "pelayanan.nomor_pelayanan",
            )
                ->join('pelayanan', 'lspop.nomor_pelayanan', '=', 'pelayanan.nomor_pelayanan')
                ->whereRaw('CONCAT(op_kd_provinsi, op_kd_kabupaten, op_kd_kecamatan, op_kd_kelurahan, op_kd_blok, no_urut, status_kolektif) = ' . $request['nop'])
                ->first();
            $updateLspop = $this->lspop->where('nomor_pelayanan', $getNomorLayanan->nomor_pelayanan)->update($inpuLspop);
            if (!$updateLspop) :
                throw new \Exception($this->outputMessage('update fail', 'LSPOP ' . $request['nop']));
            endif;

            /**
             * whatsapp notification
             */
            $getSetting = $this->checkerHelpers->settingChecker('whatsapp notif');
            if (is_null($getSetting)) :
                throw new \Exception($this->outputMessage('not found', 'whatsapp notification setting'));
            endif;

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
                $getCurrentPelayanan = $this->checkerHelpers->pelayananChecker(['nomor_pelayanan' => $getNomorLayanan->nomor_pelayanan]);
                $tanggalPendaftaran = Carbon::parse($getCurrentPelayanan->created_at)->locale('id');
                $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);

                /**
                 * send whatsapp notification
                 */
                $message = "Permohonan BPHTB";
                $message .= "\nAtas nama " . $request['nama'];
                $message .= "\nNOP " . $request['no_registrasi'];
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
            $response  = $this->success($this->outputMessage('saved', 'Peta Objek Pajak dengan NOP ' . $request['nop']));
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
    public function data()
    {
        try {
            /**
             * data peta objek pajak
             */
            $getData = $this->petaObjekPajak->select(
                "nop",
                "nama",
                "peta_objek_pajak.alamat",
                "peta_objek_pajak.created_at",
                "name",
                "photo",
                "koordinat"
            )
                ->join('users', 'peta_objek_pajak.uuid_user', '=', 'users.uuid_user')
                ->orderBy('peta_objek_pajak.id', 'desc')
                ->get();

            $data = [];
            foreach ($getData as $key => $value) :
                /**
                 * convert tanggal
                 */
                $tanggal = Carbon::parse($value->created_at)->locale('id');
                $tanggal->settings(['formatFunction' => 'translatedFormat']);

                /**
                 * set output
                 */
                $set = [
                    "nop"       => $value->nop,
                    "nama"      => $value->nama,
                    "alamat"    => $value->alamat,
                    'tanggal'   => $tanggal->format('l, j F Y ; h:i:s a'),
                    "pendaftar" => $value->name,
                    "photo"     => json_decode($value->photo),
                    "koordinat" => json_decode($value->koordinat),
                ];
                array_push($data, $set);
            endforeach;

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
     * autocomplete
     */
    public function autocomplete($nop)
    {
        try {
            /**
             * data sppt
             */
            $getSppt = $this->sppt->select(
                "NM_WP_SPPT",
                "JLN_WP_SPPT",
                "LUAS_BUMI_SPPT",
                "LUAS_BNG_SPPT",
            )
                ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = ' . $nop)
                ->first();
            if (is_null($getSppt)) :
                throw new \Exception($this->outputMessage('not found', 'SPPT'));
            endif;

            /**
             * get data lspop
             */
            $getLspop = $this->lspop->select(
                "kondisi_umum",
                "md_dalam_jenis",
                "md_dalam",
                "md_luar",
                "md_luar_jml_lt",
                "pd_dalam_jenis",
                "pd_dalam_jml_lt",
                "pd_dalam",
                "pd_luar_jenis",
                "pd_luar_jml_lt",
                "pd_luar",
                "langit_langit_jenis",
                "langit_langit_jml_lt",
                "langit_langit",
                "atap",
                "penutup_lantai_jenis",
                "penutup_lantai_jml_lt",
                "penutup_lantai",
                "daya_listrik_terpasang"
            )
                ->join('pelayanan', 'lspop.nomor_pelayanan', '=', 'pelayanan.nomor_pelayanan')
                ->whereRaw('CONCAT(op_kd_provinsi, op_kd_kabupaten, op_kd_kecamatan, op_kd_kelurahan, op_kd_blok, no_urut, status_kolektif) = ' . $nop)
                ->first();

            $data['sppt']  = $getSppt;
            $data['lspop'] = is_null($getLspop) ? [] : $getLspop;

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

<?php

namespace App\Repositories\Pelayanan\Pelayanan;

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

use App\Models\Pelayanan\Pelayanan\Pelayanan;
use App\Models\Sppt\Sppt;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\Pelayanan\Lspop\Lspop;
use App\Models\DatSubjekPajak\DatSubjekPajak;
use App\Models\DatOpBumi\DatOpBumi;
use App\Models\DatOpBangunan\DatOpBangunan;

/**
 * import helpers
 */

use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Pelayanan\PelayananRepositories;

class EloquentPelayananRepositories implements PelayananRepositories
{
    use Message, Response, Generator;

    private $pelayanan;
    private $sppt;
    private $datObjekPajak;
    private $lspop;
    private $pembayaranSppt;
    private $paginateHelpers;
    private $datSubjekPajak;
    private $datOpBumi;
    private $datOpBangunan;

    public function __construct(
        Pelayanan $pelayanan,
        Sppt $sppt,
        DatObjekPajak $datObjekPajak,
        PembayaranSppt $pembayaranSppt,
        Lspop $lspop,
        DatSubjekPajak $datSubjekPajak,
        DatOpBangunan $datOpBangunan,
        DatOpBumi $datOpBumi,
        PaginateHelpers $paginateHelpers
    ) {
        /**
         * initialize model
         */
        $this->pelayanan = $pelayanan;
        $this->sppt = $sppt;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->lspop = $lspop;
        $this->datSubjekPajak = $datSubjekPajak;
        $this->datOpBumi = $datOpBumi;
        $this->datOpBangunan = $datOpBangunan;

        /**
         * initialize helper
         */
        $this->paginateHelpers = $paginateHelpers;
    }

    /**
     * generate
     */
    public function generate()
    {
        try {
            /**
             * data setting
             */
            $nomor    = $this->nomorPelayananPbb();
            $response = $this->success('success');
            $response['nomor'] = $nomor;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * all record
     */
    public function data($param, $pageSize)
    {
        try {
            /**
             * set status verifikasi
             */
            if ($param == 'kabid' || $param == 3) $statusVerifikasi = [3];
            if ($param == 'kasubbid' || $param == 1 || $param == 'operator') $statusVerifikasi = [1];
            if ($param == 2) $statusVerifikasi = [2];
            if ($param == 4) $statusVerifikasi = [4];
            if ($param == 'superadmin') $statusVerifikasi = [1, 2, 3, 4];

            /**
             * data pelayanan
             */
            $data = $this->pelayanan->select(
                "uuid_pelayanan",
                "nomor_pelayanan",
                "pelayanan.created_at",
                "nama_lengkap",
                "id_pemohon",
                "status_verifikasi",
                "pelayanan.uuid_jenis_pelayanan",
                "pelayanan.uuid_layanan",
                "layanan",
                "jenis_layanan",
                "op_kd_kecamatan",
                "op_kd_kelurahan",
                "op_kd_blok",
                "no_urut",
                "status_kolektif",
                "alasan",
                "pelayanan.updated_at",
                "updated_by",
                "pelayanan.created_by"
            )
                ->selectRaw('(SELECT name FROM users WHERE uuid_user = pelayanan.created_by) AS pendaftar')
                ->selectRaw('(SELECT name FROM users WHERE uuid_user = pelayanan.updated_by) AS pengubah')
                ->leftJoin("layanan", "pelayanan.uuid_layanan", "=", "layanan.uuid_layanan")
                ->leftJoin("jenis_layanan", "pelayanan.uuid_jenis_pelayanan", "=", "jenis_layanan.uuid_jenis_layanan")
                ->whereIn("status_verifikasi", $statusVerifikasi)
                ->whereNotIn('jenis_layanan', ['pecah nop'])
                ->orderBy('pelayanan.id', 'desc')
                ->get();

            $output = [];
            foreach ($data as $key => $value) :

                /**
                 * verifikasi status
                 */
                $keteranganStatus = $value->status_verifikasi == 1 ? "Permohonan Baru" : ($value->status_verifikasi == 2 ? "Ditolak" : ($value->status_verifikasi == 3 ? "Diverifikasi Kasubbid" : "Ditetapkan Kabid"));

                /**
                 * convert tanggal
                 */
                $tanggalPendaftaran = Carbon::parse($value->created_at)->locale('id');
                $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);
                $tanggalUbah = Carbon::parse($value->updated_at)->locale('id');
                $tanggalUbah->settings(['formatFunction' => 'translatedFormat']);

                /**
                 * set output
                 */
                $set = [
                    "uuid_pelayanan"  => $value->uuid_pelayanan,
                    "nomor_pelayanan" => $value->nomor_pelayanan,
                    "nama_lengkap"    => $value->nama_lengkap,
                    "id_pemohon"      => $value->id_pemohon,
                    "status_verifikasi" => [
                        "value"       => $value->status_verifikasi,
                        "keterangan"  => $keteranganStatus
                    ],
                    "layanan" => [
                        "uuid"       => $value->uuid_layanan,
                        "keterangan" => $value->layanan
                    ],
                    "jenis_layanan" => [
                        "uuid"       => $value->uuid_jenis_pelayanan,
                        "keterangan" => $value->jenis_layanan
                    ],
                    'tanggal_pendaftaran' => $tanggalPendaftaran->format('l, j F Y ; h:i:s a'),
                    'tanggal_ubah' => $tanggalUbah->format('l, j F Y ; h:i:s a'),
                    "updated_by" => $value->pengubah,
                    "pendaftar" => $value->pendaftar,
                ];

                if ($param == 2) :
                    $set['alasan'] = $value->alasan;
                endif;

                if ($value->status_verifikasi == 4) :
                    $set['nop'] = $this->nop($value->op_kd_kecamatan, $value->op_kd_kelurahan, $value->op_kd_blok, $value->no_urut, $value->status_kolektif);
                endif;
                array_push($output, $set);
            endforeach;
            $collectionObject = collect($output);
            $pageSize = is_null($pageSize) ? 10 : $pageSize;
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, $pageSize);

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data realisasi by kecamatan
     */
    public function dataRealisasiKecamatan($kdKecamatan, $tahun)
    {
        try {
            $data = $this->pembayaranSppt->where(['KD_KECAMATAN' => $kdKecamatan, 'THN_PAJAK_SPPT' => $tahun])->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data piutang by kecamatan
     */
    public function dataPiutangKecamatan($kdKecamatan, $tahun)
    {
        try {
            $dataSppt = $this->sppt->select(
                'KD_PROPINSI',
                'KD_DATI2',
                'KD_KECAMATAN',
                'KD_KELURAHAN',
                'KD_BLOK',
                'NO_URUT',
                'KD_JNS_OP',
                'THN_PAJAK_SPPT',
                'NM_WP_SPPT',
                'PBB_YG_HARUS_DIBAYAR_SPPT',
                'TGL_JATUH_TEMPO_SPPT',
                'LUAS_BUMI_SPPT',
                'LUAS_BNG_SPPT'
            )
                ->where(['KD_KECAMATAN' => $kdKecamatan, 'THN_PAJAK_SPPT' => $tahun, 'STATUS_PEMBAYARAN_SPPT' => 0])
                ->get();
            $data = [];
            foreach ($dataSppt as $key => $value) :
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
                    'KD_PROPINSI' => $value->KD_PROPINSI,
                    'KD_DATI2' => $value->KD_DATI2,
                    'KD_KECAMATAN' => $value->KD_KECAMATAN,
                    'KD_KELURAHAN' => $value->KD_KELURAHAN,
                    'KD_BLOK' => $value->KD_BLOK,
                    'NO_URUT' => $value->NO_URUT,
                    'KD_JNS_OP' => $value->KD_JNS_OP,
                    'THN_PAJAK_SPPT' => $value->THN_PAJAK_SPPT,
                    'NM_WP_SPPT' => $value->NM_WP_SPPT,
                    'PBB_YG_HARUS_DIBAYAR_SPPT' => $value->PBB_YG_HARUS_DIBAYAR_SPPT,
                    'TGL_JATUH_TEMPO_SPPT' => $value->TGL_JATUH_TEMPO_SPPT,
                    'JALAN_OP' => $getObjekPajak->JALAN_OP,
                    'LUAS_BUMI_SPPT' => $value->LUAS_BUMI_SPPT,
                    'LUAS_BNG_SPPT' => $value->LUAS_BNG_SPPT
                ];
                array_push($data, $set);
            endforeach;
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data jumlah wp by kecamatan
     */
    public function dataJumlahWpKecamatan($kdKecamatan, $tahun)
    {
        try {
            $dataSppt = $this->sppt->select(
                'KD_PROPINSI',
                'KD_DATI2',
                'KD_KECAMATAN',
                'KD_KELURAHAN',
                'KD_BLOK',
                'NO_URUT',
                'KD_JNS_OP',
                'THN_PAJAK_SPPT',
                'NM_WP_SPPT',
                'PBB_YG_HARUS_DIBAYAR_SPPT',
                'TGL_JATUH_TEMPO_SPPT',
                'LUAS_BUMI_SPPT',
                'LUAS_BNG_SPPT'
            )
                ->where(['KD_KECAMATAN' => $kdKecamatan, 'THN_PAJAK_SPPT' => $tahun])
                ->get();

            $data = [];
            foreach ($dataSppt as $key => $value) :
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
                    'KD_PROPINSI' => $value->KD_PROPINSI,
                    'KD_DATI2' => $value->KD_DATI2,
                    'KD_KECAMATAN' => $value->KD_KECAMATAN,
                    'KD_KELURAHAN' => $value->KD_KELURAHAN,
                    'KD_BLOK' => $value->KD_BLOK,
                    'NO_URUT' => $value->NO_URUT,
                    'KD_JNS_OP' => $value->KD_JNS_OP,
                    'THN_PAJAK_SPPT' => $value->THN_PAJAK_SPPT,
                    'NM_WP_SPPT' => $value->NM_WP_SPPT,
                    'PBB_YG_HARUS_DIBAYAR_SPPT' => $value->PBB_YG_HARUS_DIBAYAR_SPPT,
                    'TGL_JATUH_TEMPO_SPPT' => $value->TGL_JATUH_TEMPO_SPPT,
                    'JALAN_OP' => $getObjekPajak->JALAN_OP,
                    'LUAS_BUMI_SPPT' => $value->LUAS_BUMI_SPPT,
                    'LUAS_BNG_SPPT' => $value->LUAS_BNG_SPPT
                ];
                array_push($data, $set);
            endforeach;
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data realisasi by kelurahan
     */
    public function dataRealisasiKelurahan($kdKecamatan, $kdKelurahan, $tahun)
    {
        try {
            $data = $this->pembayaranSppt->where(['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan, 'THN_PAJAK_SPPT' => $tahun])->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data piutang by kelurahan
     */
    public function dataPiutangKelurahan($kdKecamatan, $kdKelurahan, $tahun)
    {
        try {
            $dataSppt = $this->sppt->select(
                'KD_PROPINSI',
                'KD_DATI2',
                'KD_KECAMATAN',
                'KD_KELURAHAN',
                'KD_BLOK',
                'NO_URUT',
                'KD_JNS_OP',
                'THN_PAJAK_SPPT',
                'NM_WP_SPPT',
                'PBB_YG_HARUS_DIBAYAR_SPPT',
                'TGL_JATUH_TEMPO_SPPT',
                'LUAS_BUMI_SPPT',
                'LUAS_BNG_SPPT'
            )
                ->where(['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan, 'THN_PAJAK_SPPT' => $tahun, 'STATUS_PEMBAYARAN_SPPT' => 0])
                ->get();

            $data = [];
            foreach ($dataSppt as $key => $value) :
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
                    'KD_PROPINSI' => $value->KD_PROPINSI,
                    'KD_DATI2' => $value->KD_DATI2,
                    'KD_KECAMATAN' => $value->KD_KECAMATAN,
                    'KD_KELURAHAN' => $value->KD_KELURAHAN,
                    'KD_BLOK' => $value->KD_BLOK,
                    'NO_URUT' => $value->NO_URUT,
                    'KD_JNS_OP' => $value->KD_JNS_OP,
                    'THN_PAJAK_SPPT' => $value->THN_PAJAK_SPPT,
                    'NM_WP_SPPT' => $value->NM_WP_SPPT,
                    'PBB_YG_HARUS_DIBAYAR_SPPT' => $value->PBB_YG_HARUS_DIBAYAR_SPPT,
                    'TGL_JATUH_TEMPO_SPPT' => $value->TGL_JATUH_TEMPO_SPPT,
                    'JALAN_OP' => $getObjekPajak->JALAN_OP,
                    'LUAS_BUMI_SPPT' => $value->LUAS_BUMI_SPPT,
                    'LUAS_BNG_SPPT' => $value->LUAS_BNG_SPPT
                ];
                array_push($data, $set);
            endforeach;
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data jumlah wp by kelurahan
     */
    public function dataJumlahWpKelurahan($kdKecamatan, $kdKelurahan, $tahun)
    {
        try {
            $dataSppt = $this->sppt->select(
                'KD_PROPINSI',
                'KD_DATI2',
                'KD_KECAMATAN',
                'KD_KELURAHAN',
                'KD_BLOK',
                'NO_URUT',
                'KD_JNS_OP',
                'THN_PAJAK_SPPT',
                'NM_WP_SPPT',
                'PBB_YG_HARUS_DIBAYAR_SPPT',
                'TGL_JATUH_TEMPO_SPPT',
                'LUAS_BUMI_SPPT',
                'LUAS_BNG_SPPT'
            )
                ->where(['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan, 'THN_PAJAK_SPPT' => $tahun])
                ->get();
            $data = [];
            foreach ($dataSppt as $key => $value) :
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
                    'KD_PROPINSI' => $value->KD_PROPINSI,
                    'KD_DATI2' => $value->KD_DATI2,
                    'KD_KECAMATAN' => $value->KD_KECAMATAN,
                    'KD_KELURAHAN' => $value->KD_KELURAHAN,
                    'KD_BLOK' => $value->KD_BLOK,
                    'NO_URUT' => $value->NO_URUT,
                    'KD_JNS_OP' => $value->KD_JNS_OP,
                    'THN_PAJAK_SPPT' => $value->THN_PAJAK_SPPT,
                    'NM_WP_SPPT' => $value->NM_WP_SPPT,
                    'PBB_YG_HARUS_DIBAYAR_SPPT' => $value->PBB_YG_HARUS_DIBAYAR_SPPT,
                    'TGL_JATUH_TEMPO_SPPT' => $value->TGL_JATUH_TEMPO_SPPT,
                    'JALAN_OP' => $getObjekPajak->JALAN_OP,
                    'LUAS_BUMI_SPPT' => $value->LUAS_BUMI_SPPT,
                    'LUAS_BNG_SPPT' => $value->LUAS_BNG_SPPT
                ];
                array_push($data, $set);
            endforeach;
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * search data
     */
    public function search($param, $pageSize, $request)
    {
        try {
            $key = $request->key;

            /**
             * set status verifikasi
             */
            if ($param == 'kabid' || $param == 3) $statusVerifikasi = [3];
            if ($param == 'kasubbid' || $param == 1 || $param == 'operator') $statusVerifikasi = [1];
            if ($param == 2) $statusVerifikasi = [2];
            if ($param == 4) $statusVerifikasi = [4];
            if ($param == 'superadmin') $statusVerifikasi = [1, 2, 3, 4];

            /**
             * data pelayanan
             */
            $data = $this->pelayanan->select(
                "uuid_pelayanan",
                "nomor_pelayanan",
                "pelayanan.created_at",
                "nama_lengkap",
                "id_pemohon",
                "status_verifikasi",
                "pelayanan.uuid_jenis_pelayanan",
                "pelayanan.uuid_layanan",
                "layanan",
                "jenis_layanan",
                "op_kd_kecamatan",
                "op_kd_kelurahan",
                "op_kd_blok",
                "no_urut",
                "status_kolektif"
            )
                ->leftJoin("layanan", "pelayanan.uuid_layanan", "=", "layanan.uuid_layanan")
                ->leftJoin("jenis_layanan", "pelayanan.uuid_jenis_pelayanan", "=", "jenis_layanan.uuid_jenis_layanan")
                ->whereIn("status_verifikasi", $statusVerifikasi)
                ->where(function ($query) use ($key) {
                    $query->where("nomor_pelayanan", "like", "%" . $key . "%")
                        ->orWhere("nama_lengkap", "like", "%" . $key . "%")
                        ->orWhere("id_pemohon", "like", "%" . $key . "%")
                        ->orWhere("status_verifikasi", $key)
                        ->orWhere("layanan", "like", "%" . $key . "%")
                        ->orWhere("jenis_layanan", "like", "%" . $key . "%");
                })
                ->orderBy('pelayanan.id', 'desc')
                ->get();

            $output = [];
            foreach ($data as $key => $value) :

                /**
                 * verifikasi status
                 */
                $keteranganStatus = $value->status_verifikasi == 1 ? "Permohonan Baru" : ($value->status_verifikasi == 2 ? "Ditolak" : ($value->status_verifikasi == 3 ? "Diverifikasi Kasubbid" : "Ditetapkan Kabid"));

                /**
                 * convert tanggal
                 */
                $tanggalPendaftaran = Carbon::parse($value->created_at)->locale('id');
                $tanggalPendaftaran->settings(['formatFunction' => 'translatedFormat']);

                /**
                 * set output
                 */
                $set = [
                    "uuid_pelayanan"  => $value->uuid_pelayanan,
                    "nomor_pelayanan" => $value->nomor_pelayanan,
                    "nama_lengkap"    => $value->nama_lengkap,
                    "id_pemohon"      => $value->id_pemohon,
                    "status_verifikasi" => [
                        "value"       => $value->status_verifikasi,
                        "keterangan"  => $keteranganStatus
                    ],
                    "layanan" => [
                        "uuid"       => $value->uuid_layanan,
                        "keterangan" => $value->layanan
                    ],
                    "jenis_layanan" => [
                        "uuid"       => $value->uuid_jenis_pelayanan,
                        "keterangan" => $value->jenis_layanan
                    ],
                    'tanggal_pendaftaran' => $tanggalPendaftaran->format('l, j F Y ; h:i:s a')
                ];
                if ($value->status_verifikasi == 4) :
                    $set['nop'] = $this->nop($value->op_kd_kecamatan, $value->op_kd_kelurahan, $value->op_kd_blok, $value->no_urut, $value->status_kolektif);
                endif;
                array_push($output, $set);
            endforeach;
            $collectionObject = collect($output);
            $pageSize = is_null($pageSize) ? 10 : $pageSize;
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, $pageSize);

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * count data
     */
    public function countSppt($kdKecamatan, $kdKelurahan, $kdBlok, $noUrutAwal, $noUrutAkhir, $statusKolektif)
    {
        try {
            /**
             * data sppt
             */
            $data = $this->sppt->selectRaw('count(*) as jumlah_data')
                ->where([
                    'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
                    'KD_DATI2' => globalAttribute()['kdKota'],
                    'KD_KECAMATAN' => $kdKecamatan,
                    'KD_KELURAHAN' => $kdKelurahan,
                    'KD_BLOK' => $kdBlok,
                    'KD_JNS_OP' => $statusKolektif,
                ])
                ->whereBetween('NO_URUT', [$noUrutAwal, $noUrutAkhir])
                ->first();

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
     * count nop data
     */
    public function countNop($kdKecamatan, $kdKelurahan)
    {
        try {

            if ($kdKecamatan && !$kdKelurahan) :
                $dataTotal = $this->datObjekPajak->selectRaw('COUNT(NO_URUT) AS jumlah_nop')
                    ->selectRaw('CONVERT(SUM(TOTAL_LUAS_BUMI),SIGNED INTEGER) AS luas_bumi')
                    ->selectRaw('CONVERT(SUM(TOTAL_LUAS_BNG),SIGNED INTEGER) AS luas_bangunan')
                    ->where('KD_KECAMATAN', $kdKecamatan)
                    ->get();

                $dataRincian = $this->datObjekPajak->select(
                    'KD_KECAMATAN',
                    'KD_KELURAHAN',
                )
                    ->selectRaw('COUNT(NO_URUT) AS jumlah_nop')
                    ->selectRaw('CONVERT(SUM(TOTAL_LUAS_BUMI),SIGNED INTEGER) AS luas_bumi')
                    ->selectRaw('CONVERT(SUM(TOTAL_LUAS_BNG),SIGNED INTEGER) AS luas_bangunan')
                    ->whereIn('KD_KECAMATAN', [$kdKecamatan])
                    ->whereIn('KD_KELURAHAN', ['001', '002', '003', '004', '005', '006', '007', '008', '009'])
                    ->groupBy('KD_KECAMATAN', 'KD_KELURAHAN')
                    ->get();
            elseif ($kdKecamatan && $kdKelurahan) :
                $dataTotal = $this->datObjekPajak->selectRaw('COUNT(NO_URUT) AS jumlah_nop')
                    ->selectRaw('CONVERT(SUM(TOTAL_LUAS_BUMI),SIGNED INTEGER) AS luas_bumi')
                    ->selectRaw('CONVERT(SUM(TOTAL_LUAS_BNG),SIGNED INTEGER) AS luas_bangunan')
                    ->where(['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan])
                    ->get();

                $dataRincian = $this->datObjekPajak->select(
                    'KD_KECAMATAN',
                    'KD_KELURAHAN',
                    'KD_BLOK'
                )
                    ->selectRaw('COUNT(NO_URUT) AS jumlah_nop')
                    ->selectRaw('CONVERT(SUM(TOTAL_LUAS_BUMI),SIGNED INTEGER) AS luas_bumi')
                    ->selectRaw('CONVERT(SUM(TOTAL_LUAS_BNG),SIGNED INTEGER) AS luas_bangunan')
                    ->whereIn('KD_KECAMATAN', [$kdKecamatan])
                    ->whereIn('KD_KELURAHAN', [$kdKelurahan])
                    ->whereRaw('KD_BLOK BETWEEN "001" AND "035"')
                    ->groupBy('KD_KECAMATAN', 'KD_KELURAHAN', 'KD_BLOK')
                    ->get();
            endif;

            /**
             * set response
             */
            $data = [
                'total' => $dataTotal,
                'rincian' => $dataRincian
            ];
            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

<?php

namespace App\Repositories\Sppt\Sppt;

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
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\TagihanKolektor\TagihanKolektor;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Sppt\Sppt\SpptRepositories;

class EloquentSpptRepositories implements SpptRepositories
{
    use Message, Response, Generator, Calculation;

    private $sppt;
    private $pembayaranSppt;
    private $datObjekPajak;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $tagihanKolektor;
    private $datetime;
    private $year;
    private $storageTunggakan;

    public function __construct(
        Sppt $sppt,
        TagihanKolektor $tagihanKolektor,
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
        $this->tagihanKolektor = $tagihanKolektor;

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
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->year = Carbon::now()->format('Y');
        $this->storageTunggakan = path('tunggakan');
    }

    /**
     * all record
     */
    public function data($pageSize)
    {
        try {
            /**
             * data sppt
             */
            $pageSize = is_null($pageSize) ? 10 : $pageSize;
            $data   = $this->sppt->orderByRaw('THN_PAJAK_SPPT DESC, KD_KECAMATAN ASC, KD_KELURAHAN ASC, KD_BLOK ASC, NO_URUT ASC')->paginate($pageSize);

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
     * riwayat pembayaran
     */
    public function history($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $kdJnsOp, $request)
    {
        try {
            /**
             * set condition for status
             */
            $where = [
                'KD_PROPINSI'  => $this->provinsi,
                'KD_DATI2'     => $this->kabupaten,
                'KD_KECAMATAN' => $kdKecamatan,
                'KD_KELURAHAN' => $kdKelurahan,
                'KD_BLOK'      => $kdBlok,
                'NO_URUT'      => $noUrut,
                'KD_JNS_OP'    => $kdJnsOp,
            ];
            if ($request != null) :
                $where = array_merge($where, ['STATUS_PEMBAYARAN_SPPT' => $request]);
            endif;

            $dataSppt = $this->sppt->select(
                'THN_PAJAK_SPPT',
                'NM_WP_SPPT',
                'kode_bayar',
                'PBB_YG_HARUS_DIBAYAR_SPPT',
                'TGL_JATUH_TEMPO_SPPT',
                DB::raw("CASE WHEN STATUS_PEMBAYARAN_SPPT = 1 THEN 'sudah dibayar' WHEN STATUS_PEMBAYARAN_SPPT = 0 THEN 'belum dibayar' ELSE 'status tidak valid' END AS status_pembayaran"),
                DB::raw('TIMESTAMPDIFF(MONTH, TGL_JATUH_TEMPO_SPPT, CURDATE()) +
        IF(DAY(CURDATE()) < DAY(TGL_JATUH_TEMPO_SPPT), -1, 0) AS jumlah_bulan')
            )
                ->where($where)
                ->orderByRaw('THN_PAJAK_SPPT DESC, KD_KECAMATAN ASC, KD_KELURAHAN ASC, KD_BLOK ASC, NO_URUT ASC')
                ->get();

            $dataList = [];
            $totaltagihan = count($dataSppt);
            $pokok = 0;
            $denda = 0;
            $dibayar = 0;
            foreach ($dataSppt as $key => $value) :

                /**
                 * perhitungan bulan
                 */

                $jumlahBulan = $this->perhitunganBulanDendaPBB($value->TGL_JATUH_TEMPO_SPPT);

                /**
                 * get data pembayaran sppt
                 */
                $dataPembayaranSppt = $this->pembayaranSppt->select('DENDA_SPPT', 'JML_SPPT_YG_DIBAYAR', 'TGL_PEMBAYARAN_SPPT')
                    ->where([
                        'KD_PROPINSI'    => $this->provinsi,
                        'KD_DATI2'       => $this->kabupaten,
                        'KD_KECAMATAN'   => $kdKecamatan,
                        'KD_KELURAHAN'   => $kdKelurahan,
                        'KD_BLOK'        => $kdBlok,
                        'NO_URUT'        => $noUrut,
                        'KD_JNS_OP'      => $kdJnsOp,
                        'THN_PAJAK_SPPT' => $value->THN_PAJAK_SPPT
                    ])
                    ->first();
                $dibayar = is_null($dataPembayaranSppt) ? 0 : $dataPembayaranSppt->JML_SPPT_YG_DIBAYAR;
                $tanggal_dibayar = is_null($dataPembayaranSppt) ? null : $dataPembayaranSppt->TGL_PEMBAYARAN_SPPT;

                /**
                 * perhitungan denda
                 */
                if (is_null($tanggal_dibayar)) :
                    $denda = $this->dendaPbb($jumlahBulan, $value->PBB_YG_HARUS_DIBAYAR_SPPT, $value->THN_PAJAK_SPPT);
                else :
                    $denda = $dataPembayaranSppt->DENDA_SPPT;
                endif;

                $set = [
                    'tahun_pajak'         => $value->THN_PAJAK_SPPT,
                    'nama_wp'             => $value->NM_WP_SPPT,
                    'kode_bayar'          => $value->kode_bayar,
                    'jumlah_tagihan'      => $value->PBB_YG_HARUS_DIBAYAR_SPPT,
                    'tanggal_jatuh_tempo' => $value->TGL_JATUH_TEMPO_SPPT,
                    'status'              => $value->status_pembayaran,
                    'denda'               => $denda,
                    'dibayar'             => $dibayar,
                    'tanggal_dibayar'     => $tanggal_dibayar,
                    'jumlah_bulan'        => $jumlahBulan
                ];
                $pokok += $value->PBB_YG_HARUS_DIBAYAR_SPPT;
                $denda += $denda;
                $dibayar += $dibayar;
                array_push($dataList, $set);
            endforeach;

            /**
             * get dat objek pajak
             */
            $nop = $this->provinsi .  $this->kabupaten . $kdKecamatan . $kdKelurahan . $kdBlok . $noUrut . $kdJnsOp;
            $getDatObjekPajak = $this->datObjekPajak->select('JALAN_OP', 'BLOK_KAV_NO_OP')
                ->whereRaw('CONCAT(KD_PROPINSI, KD_DATI2, KD_KECAMATAN, KD_KELURAHAN, KD_BLOK, NO_URUT, KD_JNS_OP) = "' . $nop . '"')
                ->first();
            if (is_null($getDatObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'data objek pajak'));
            endif;

            /**
             * get kecamatan
             */
            $getKecamatan = $this->checkerHelpers->refrensiKecamatanChecker(['KD_KECAMATAN' => $kdKecamatan]);
            if (is_null($getKecamatan)) :
                throw new \Exception($this->outputMessage('not found', 'kecamatan'));
            endif;

            /**
             * get kelurahan
             */
            $getKelurahan = $this->checkerHelpers->refrensiKelurahanChecker(['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan]);
            if (is_null($getKelurahan)) :
                throw new \Exception($this->outputMessage('not found', 'kelurahan'));
            endif;

            $output = [
                'information' => [
                    'nop' => $nop,
                    'alamat' => $getDatObjekPajak->JALAN_OP,
                    'total_tagihan' => $totaltagihan,
                    'pokok' => $pokok,
                    'denda' => $denda,
                    'dibayar' => $dibayar,
                    'kecamatan' => $getKecamatan->NM_KECAMATAN,
                    'kelurahan' => $getKelurahan->NM_KELURAHAN,
                    'no_sertifikat' => $getDatObjekPajak->BLOK_KAV_NO_OP
                ],
                'list' => $dataList,
            ];

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($output)), $output);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * pencarian
     */
    public function search($kdKecamatan, $kdKelurahan, $nama, $kdBlok, $noUrut, $statusKolektif)
    {
        try {
            $select = DB::raw("THN_PAJAK_SPPT AS tahun, 
            NM_WP_SPPT AS nama,
            CONCAT(KD_PROPINSI , '.', KD_DATI2 , '.', KD_KECAMATAN,'.',KD_KELURAHAN ,'.',KD_BLOK ,'.',NO_URUT ,'.',KD_JNS_OP) AS nop,
            PBB_YG_HARUS_DIBAYAR_SPPT AS tagihan, KD_BLOK, NO_URUT, KD_JNS_OP,
            LUAS_BUMI_SPPT AS luas_tanah, LUAS_BNG_SPPT AS luas_bangunan, KD_KECAMATAN, KD_KELURAHAN");

            $orderBy = 'THN_PAJAK_SPPT DESC, KD_KECAMATAN ASC, KD_KELURAHAN ASC, KD_BLOK ASC, NO_URUT ASC';
            $where = [
                'KD_PROPINSI'  => $this->provinsi,
                'KD_DATI2'     => $this->kabupaten,
                'KD_KECAMATAN'   => $kdKecamatan,
                'KD_KELURAHAN'   => $kdKelurahan,
            ];

            /**
             * by NOP or nama
             */
            if (empty($nama)) :
                $dataSppt = $this->sppt->select($select)
                    ->where(array_merge(
                        $where,
                        [
                            'KD_BLOK'     => $kdBlok,
                            'NO_URUT'     => $noUrut,
                            'KD_JNS_OP'   => $statusKolektif
                        ]
                    ))
                    ->orderByRaw($orderBy)
                    ->first();

                $datObjekPajak = $this->datObjekPajak->select('JALAN_OP', 'BLOK_KAV_NO_OP')
                    ->where(array_merge($where, [
                        'KD_BLOK'        => $dataSppt->KD_BLOK,
                        'NO_URUT'        => $dataSppt->NO_URUT,
                        'KD_JNS_OP'      => $dataSppt->KD_JNS_OP
                    ]))
                    ->first();

                $alamat = is_null($datObjekPajak) ? NULL : $datObjekPajak->JALAN_OP;
                $noSertifikat = is_null($datObjekPajak) ? NULL : $datObjekPajak->BLOK_KAV_NO_OP;

                $output = [
                    'nop'     => $dataSppt->nop,
                    'tahun'   => $dataSppt->tahun,
                    'nama'    => $dataSppt->nama,
                    'tagihan' => $dataSppt->tagihan,
                    'alamat'  => $alamat,
                    'luas_tanah' => $dataSppt->luas_tanah,
                    'luas_bangunan' => $dataSppt->luas_bangunan,
                    'no_sertifikat' => $noSertifikat
                ];
            else :
                $dataSppt = $this->sppt->select($select);

                if ($kdKecamatan != 'all' && $kdKelurahan != 'all') :
                    $dataSppt = $dataSppt->where($where);
                endif;

                $dataSppt = $dataSppt->where('NM_WP_SPPT', 'like', "%$nama%")
                    ->groupBy('NM_WP_SPPT')
                    ->orderByRaw($orderBy)
                    ->get();

                $output = [];
                foreach ($dataSppt as $key => $value) :
                    $datObjekPajak = $this->datObjekPajak->select('JALAN_OP', 'BLOK_KAV_NO_OP')
                        ->where([
                            'KD_KECAMATAN'   => $value->KD_KECAMATAN,
                            'KD_KELURAHAN'   => $value->KD_KELURAHAN,
                            'KD_BLOK'        => $value->KD_BLOK,
                            'NO_URUT'        => $value->NO_URUT,
                            'KD_JNS_OP'      => $value->KD_JNS_OP
                        ])
                        ->first();

                    $alamat = is_null($datObjekPajak) ? NULL : $datObjekPajak->JALAN_OP;
                    $noSertifikat = is_null($datObjekPajak) ? NULL : $datObjekPajak->BLOK_KAV_NO_OP;

                    $set = [
                        'nop'     => $value->nop,
                        'tahun'   => $value->tahun,
                        'nama'    => $value->nama,
                        'tagihan' => $value->tagihan,
                        'alamat'  => $alamat,
                        'luas_tanah' => $value->luas_tanah,
                        'luas_bangunan' => $value->luas_bangunan,
                        'no_sertifikat' => $noSertifikat
                    ];
                    array_push($output, $set);
                endforeach;
            endif;

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($output)), $output);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * search by nop & tahun
     */
    public function searchByNopTahun($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif, $tahun)
    {
        try {
            /**
             * data
             */
            $dataSppt   = $this->sppt->select('NM_WP_SPPT', 'KD_DATI2', 'KD_KECAMATAN', 'KD_KELURAHAN', 'JLN_WP_SPPT', 'NJOP_BUMI_SPPT', 'NJOP_BNG_SPPT')
                ->where([
                    'KD_KECAMATAN'   => $kdKecamatan,
                    'KD_KELURAHAN'   => $kdKelurahan,
                    'KD_BLOK'        => $kdBlok,
                    'NO_URUT'        => $noUrut,
                    'KD_JNS_OP'      => $statusKolektif,
                    'THN_PAJAK_SPPT' => $tahun,
                ])
                ->orderByRaw('THN_PAJAK_SPPT DESC, KD_KECAMATAN ASC, KD_KELURAHAN ASC, KD_BLOK ASC, NO_URUT ASC')
                ->first();
            if (is_null($dataSppt)) :
                $nop = $this->nop($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif);
                throw new \Exception($this->outputMessage('not found', 'NOP ' . $nop . ' Tahun ' . $tahun));
            endif;

            $dataObjekPajak   = $this->datObjekPajak->select('JALAN_OP')
                ->where([
                    'KD_KECAMATAN'   => $kdKecamatan,
                    'KD_KELURAHAN'   => $kdKelurahan,
                    'KD_BLOK'        => $kdBlok,
                    'NO_URUT'        => $noUrut,
                    'KD_JNS_OP'      => $statusKolektif
                ])
                ->orderByRaw('KD_KECAMATAN ASC, KD_KELURAHAN ASC, KD_BLOK ASC, NO_URUT ASC')
                ->first();
            $data = [
                'NM_WP_SPPT'     => $dataSppt->NM_WP_SPPT,
                'KD_DATI2'       => $dataSppt->KD_DATI2,
                'KD_KECAMATAN'   => $dataSppt->KD_KECAMATAN,
                'KD_KELURAHAN'   => $dataSppt->KD_KELURAHAN,
                'JLN_WP_SPPT'    => $dataSppt->JLN_WP_SPPT,
                'NJOP_BUMI_SPPT' => $dataSppt->NJOP_BUMI_SPPT,
                'NJOP_BNG_SPPT'  => $dataSppt->NJOP_BNG_SPPT,
                'JALAN_OP'       => $dataObjekPajak->JALAN_OP,
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
     * pencarian by kpt
     */
    public function searchByKtp($request)
    {
        try {

            $datObjekPajak = $this->datObjekPajak->select('KD_KECAMATAN', 'KD_KELURAHAN', 'KD_BLOK', 'NO_URUT', 'KD_JNS_OP', 'JALAN_OP')
                ->where('SUBJEK_PAJAK_ID', $request->noKtp)
                ->get();

            $output = [];
            foreach ($datObjekPajak as $key => $value) :

                $dataSppt = $this->sppt->select(DB::raw("THN_PAJAK_SPPT AS tahun, 
                NM_WP_SPPT AS nama,
                CONCAT(KD_PROPINSI , '.', KD_DATI2 , '.', KD_KECAMATAN,'.',KD_KELURAHAN ,'.',KD_BLOK ,'.',NO_URUT ,'.',KD_JNS_OP) AS nop,
                PBB_YG_HARUS_DIBAYAR_SPPT AS tagihan"))
                    ->where([
                        'KD_PROPINSI'  => $this->provinsi,
                        'KD_DATI2'     => $this->kabupaten,
                        'KD_KECAMATAN' => $value->KD_KECAMATAN,
                        'KD_KELURAHAN' => $value->KD_KELURAHAN,
                        'KD_BLOK'      => $value->KD_BLOK,
                        'NO_URUT'      => $value->NO_URUT,
                        'KD_JNS_OP'    => $value->KD_JNS_OP
                    ])
                    ->orderByRaw('THN_PAJAK_SPPT DESC, KD_KECAMATAN ASC, KD_KELURAHAN ASC, KD_BLOK ASC, NO_URUT ASC')
                    ->get();
                foreach ($dataSppt as $key => $valueSppt) :

                    $set = [
                        'nop'     => $valueSppt->nop,
                        'tahun'   => $valueSppt->tahun,
                        'nama'    => $valueSppt->nama,
                        'tagihan' => $valueSppt->tagihan,
                        'alamat'  => $value->JALAN_OP,
                    ];
                    array_push($output, $set);
                endforeach;
            endforeach;

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($output)), $output);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data blok
     */
    public function dataBlok($kdKecamatan, $kdKelurahan)
    {
        try {

            $data = $this->sppt->select('sppt.KD_BLOK AS kd_blok', DB::raw('COUNT(DISTINCT CONCAT(sppt.KD_PROPINSI, sppt.KD_DATI2, sppt.KD_KECAMATAN, sppt.KD_KELURAHAN, sppt.KD_BLOK, sppt.NO_URUT, sppt.KD_JNS_OP)) AS jumlah_data'))
                ->join('ref_kecamatan', function ($join) use ($kdKecamatan) {
                    $join->on('sppt.KD_KECAMATAN', '=', 'ref_kecamatan.KD_KECAMATAN')
                        ->where('ref_kecamatan.KD_KECAMATAN', '=', $kdKecamatan);
                })
                ->join('ref_kelurahan', function ($join) use ($kdKecamatan, $kdKelurahan) {
                    $join->on('sppt.KD_KELURAHAN', '=', 'ref_kelurahan.KD_KELURAHAN')
                        ->where('ref_kelurahan.KD_KECAMATAN', '=', $kdKecamatan)
                        ->where('ref_kelurahan.KD_KELURAHAN', '=', $kdKelurahan);
                })
                ->where('sppt.KD_KECAMATAN', '=', $kdKecamatan)
                ->where('sppt.KD_KELURAHAN', '=', $kdKelurahan)
                ->where('sppt.STATUS_CETAK_SPPT', '<>', 9)
                ->groupBy('sppt.KD_BLOK')
                ->paginate(10);

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
     * get data by kd blok
     */
    public function dataNopByBlok($kdKecamatan, $kdKelurahan, $kdBlok)
    {
        try {
            /**
             * data tunggakan
             */
            $data = $this->sppt->select(
                DB::raw('CONCAT(sppt.KD_PROPINSI,sppt.KD_DATI2,sppt.KD_KECAMATAN,sppt.KD_KELURAHAN,sppt.KD_BLOK,sppt.NO_URUT,sppt.KD_JNS_OP) as nop'),
                'NM_KECAMATAN AS nama_kecamatan',
                'NM_KELURAHAN AS nama_kelurahan',
                'NM_WP_SPPT AS nama_wp',
                'JALAN_OP AS alamat_sppt',
                'THN_PAJAK_SPPT AS thn_sppt',
                'PBB_YG_HARUS_DIBAYAR_SPPT AS pbb_yg_harus_dibayar_sppt'
            )
                ->join('ref_kecamatan', function ($join) use ($kdKecamatan) {
                    $join->on('sppt.KD_KECAMATAN', '=', 'ref_kecamatan.KD_KECAMATAN')
                        ->where('ref_kecamatan.KD_KECAMATAN', '=', $kdKecamatan);
                })
                ->join('ref_kelurahan', function ($join) use ($kdKecamatan, $kdKelurahan) {
                    $join->on('sppt.KD_KELURAHAN', '=', 'ref_kelurahan.KD_KELURAHAN')
                        ->where('ref_kelurahan.KD_KECAMATAN', '=', $kdKecamatan)
                        ->where('ref_kelurahan.KD_KELURAHAN', '=', $kdKelurahan);
                })
                ->join('dat_objek_pajak', function ($join) {
                    $join->on(DB::raw('CONCAT(dat_objek_pajak.KD_PROPINSI, dat_objek_pajak.KD_DATI2, dat_objek_pajak.KD_KECAMATAN, dat_objek_pajak.KD_KELURAHAN, dat_objek_pajak.KD_BLOK, dat_objek_pajak.NO_URUT, dat_objek_pajak.KD_JNS_OP)'), '=', DB::raw('CONCAT(sppt.KD_PROPINSI, sppt.KD_DATI2, sppt.KD_KECAMATAN, sppt.KD_KELURAHAN, sppt.KD_BLOK, sppt.NO_URUT, sppt.KD_JNS_OP)'));
                })
                ->where('sppt.KD_BLOK', '=', $kdBlok)
                ->where('sppt.STATUS_CETAK_SPPT', '<>', 9)
                ->groupBy('nop')
                ->paginate(100);

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
     * data blok selesai
     */
    public function dataBlokSelesai($kdKecamatan, $kdKelurahan, $uuidUser)
    {
        try {

            $data = $this->sppt->select([
                'KD_BLOK as kd_blok',
                DB::raw('COUNT(*) AS jumlah_data'),
            ])
                ->where([
                    'KD_KECAMATAN' => [$kdKecamatan],
                    'KD_KELURAHAN' => [$kdKelurahan],
                    'STATUS_CETAK_SPPT' => 9,
                    'updated_by' => $uuidUser
                ])
                ->groupBy('KD_BLOK')
                ->paginate(10);

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
     * get data by kd blok selesai
     */
    public function dataNopByBlokSelesai($kdBlok)
    {
        try {
            /**
             * where condition
             */
            $role = authAttribute()['role'];
            if ($role == 'petugas lapangan' || $role == 'kolektor') :
                $where = [
                    'sppt.KD_KECAMATAN' => [authAttribute()['kd_kecamatan']],
                    'sppt.KD_KELURAHAN' => [authAttribute()['kd_kelurahan']],
                    'sppt.KD_BLOK' => $kdBlok,
                    'STATUS_CETAK_SPPT' => 9,
                    'updated_by' => authAttribute()['id']
                ];
            else :
                $where = ['STATUS_CETAK_SPPT' => 9, 'THN_PAJAK_SPPT' => $this->year];
            endif;

            /**
             * get data 
             */
            $data = $this->sppt->select(
                DB::raw('CONCAT(sppt.KD_PROPINSI,sppt.KD_DATI2,sppt.KD_KECAMATAN,sppt.KD_KELURAHAN,sppt.KD_BLOK,sppt.NO_URUT,sppt.KD_JNS_OP) as nop'),
                DB::raw('(SELECT `NM_KECAMATAN` FROM `ref_kecamatan` WHERE `KD_KECAMATAN` = sppt.KD_KECAMATAN LIMIT 1) AS `nama_kecamatan`'),
                DB::raw('(SELECT `NM_KELURAHAN` FROM `ref_kelurahan`WHERE `KD_KELURAHAN` = sppt.KD_KELURAHAN AND `KD_KECAMATAN` = sppt.KD_KECAMATAN LIMIT 1) AS `nama_kelurahan`'),
                'NM_WP_SPPT AS nama_wp',
                'JALAN_OP AS alamat_sppt',
                'THN_PAJAK_SPPT AS thn_sppt',
                'PBB_YG_HARUS_DIBAYAR_SPPT AS pbb_yg_harus_dibayar_sppt',
                'updated_at',
                'kategori'
            )
                ->join('dat_objek_pajak', function ($join) {
                    $join->on(DB::raw('CONCAT(dat_objek_pajak.KD_PROPINSI, dat_objek_pajak.KD_DATI2, dat_objek_pajak.KD_KECAMATAN, dat_objek_pajak.KD_KELURAHAN, dat_objek_pajak.KD_BLOK, dat_objek_pajak.NO_URUT, dat_objek_pajak.KD_JNS_OP)'), '=', DB::raw('CONCAT(sppt.KD_PROPINSI, sppt.KD_DATI2, sppt.KD_KECAMATAN, sppt.KD_KELURAHAN, sppt.KD_BLOK, sppt.NO_URUT, sppt.KD_JNS_OP)'));
                })
                ->where($where)
                ->paginate(10);

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
     * get nop selesai
     */
    public function nopSelesai($nop)
    {
        try {
            $kdProvinsi  = substr($nop, 0, 2);
            $kdKabupaten = substr($nop, 2, 2);
            $kdKecamatan = substr($nop, 4, 3);
            $kdKelurahan = substr($nop, 7, 3);
            $kdBlok      = substr($nop, 10, 3);
            $noUrut      = substr($nop, 13, 4);
            $kdJenisOp   = substr($nop, 17, 1);

            /**
             * get data sppt
             */
            $dataSppt = $this->sppt->select(
                'THN_PAJAK_SPPT',
                'NM_WP_SPPT',
                'kode_bayar',
                'PBB_YG_HARUS_DIBAYAR_SPPT',
                'TGL_JATUH_TEMPO_SPPT',
                'latitude',
                'longitude',
                DB::raw('CASE WHEN photo IS NULL THEN NULL
        ELSE CONCAT("' . url($this->storageTunggakan) . '/", photo) END AS photo'),
                DB::raw('TIMESTAMPDIFF(MONTH, TGL_JATUH_TEMPO_SPPT, CURDATE()) +
        IF(DAY(CURDATE()) < DAY(TGL_JATUH_TEMPO_SPPT), -1, 0) AS jumlah_bulan'),
                'name',
                'kategori',
                'sppt.updated_at',
                'LUAS_BUMI_SPPT',
                'LUAS_BNG_SPPT',
                'NJOP_BUMI_SPPT',
                'NJOP_BNG_SPPT',
                'keterangan_photo'
            )
                ->join('users', 'sppt.updated_by', '=', 'users.uuid_user')
                ->where('STATUS_CETAK_SPPT', 9)
                ->whereRaw('CONCAT(sppt.KD_PROPINSI,sppt.KD_DATI2,sppt.KD_KECAMATAN,sppt.KD_KELURAHAN,sppt.KD_BLOK,NO_URUT,sppt.KD_JNS_OP) = "' . $nop . '"')
                ->orderBy('THN_PAJAK_SPPT', 'DESC')
                ->first();
            if (is_null($dataSppt)) :
                throw new \Exception($this->outputMessage('not found', 'NOP ' . $nop));
            endif;

            /**
             * get OP address
             */
            $getDataObjekPajak = $this->datObjekPajak->select('JALAN_OP')
                ->where([
                    'KD_PROPINSI'  => $kdProvinsi,
                    'KD_DATI2'     => $kdKabupaten,
                    'KD_KECAMATAN' => $kdKecamatan,
                    'KD_KELURAHAN' => $kdKelurahan,
                    'KD_BLOK'      => $kdBlok,
                    'NO_URUT'      => $noUrut,
                    'KD_JNS_OP'    => $kdJenisOp
                ])
                ->first();
            if (is_null($getDataObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', ' data objek pajak'));
            endif;

            /**
             * tanggal jatuh tempo convert to locale id
             */
            $tanggalJatuhTempo = Carbon::parse($dataSppt->TGL_JATUH_TEMPO_SPPT)->locale('id');
            $tanggalJatuhTempo->settings(['formatFunction' => 'translatedFormat']);

            /**
             * perhitungan denda
             */
            $denda = $this->dendaPbb($dataSppt->jumlah_bulan, $dataSppt->PBB_YG_HARUS_DIBAYAR_SPPT, $dataSppt->THN_PAJAK_SPPT);

            /**
             * tanggal update convert to locale id
             */
            $tanggalUpdate = Carbon::parse($dataSppt->updated_at)->locale('id');
            $tanggalUpdate->settings(['formatFunction' => 'translatedFormat']);

            /**
             * count njop per M
             */
            $njopBumiPermeter = $dataSppt->LUAS_BUMI_SPPT == 0 ? 0 : $dataSppt->NJOP_BUMI_SPPT / $dataSppt->LUAS_BUMI_SPPT;
            $njopBangunanPermeter = $dataSppt->LUAS_BNG_SPPT == 0 ? 0 : $dataSppt->NJOP_BNG_SPPT / $dataSppt->LUAS_BNG_SPPT;

            $data = [
                'tahun_pajak'            => $dataSppt->THN_PAJAK_SPPT,
                'nama_wp'                => $dataSppt->NM_WP_SPPT,
                'kode_bayar'             => $dataSppt->kode_bayar,
                'jumlah_tagihan'         => $dataSppt->PBB_YG_HARUS_DIBAYAR_SPPT,
                'tanggal_jatuh_tempo'    => $tanggalJatuhTempo->format('j F Y'),
                'latitude'               => $dataSppt->latitude,
                'longitude'              => $dataSppt->longitude,
                'photo'                  => $dataSppt->photo,
                'denda'                  => $denda,
                'petugas'                => $dataSppt->name,
                'kategori'               => $dataSppt->kategori,
                'updated_at'             => $tanggalUpdate->format('j F Y, H:i:s'),
                'luas_bumi'              => $dataSppt->LUAS_BUMI_SPPT,
                'luas_bangunan'          => $dataSppt->LUAS_BNG_SPPT,
                'njop_bumi'              => $dataSppt->NJOP_BUMI_SPPT,
                'njop_bangunan'          => $dataSppt->NJOP_BNG_SPPT,
                'njop_bumi_permeter'     => $njopBumiPermeter,
                'njop_bangunan_permeter' => $njopBangunanPermeter,
                'alamat_op'              => $getDataObjekPajak->JALAN_OP,
                'keterangan_photo'       => $dataSppt->keterangan_photo,
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
}

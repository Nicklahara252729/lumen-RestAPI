<?php

namespace App\Repositories\Bank\Bpn;

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
use App\Traits\Generator;

/**
 * import models
 */

use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\Sppt\Sppt;
use App\Models\Pelayanan\PelayananBphtb\PelayananBphtb;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Bank\Bpn\BpnRepositories;

class EloquentBpnRepositories implements BpnRepositories
{
    use Message, Response, Generator;

    private $checkerHelpers;
    private $secondDb;
    private $thirdDb;
    private $datObjekPajak;
    private $sppt;
    private $pelayananBphtb;
    private $year;

    public function __construct(
        DatObjekPajak $datObjekPajak,
        Sppt $sppt,
        PelayananBphtb $pelayananBphtb,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->datObjekPajak = $datObjekPajak;
        $this->sppt = $sppt;
        $this->pelayananBphtb = $pelayananBphtb;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->secondDb = DB::connection('second_mysql');
        $this->thirdDb = DB::connection('third_mysql');
        $this->year = Carbon::now()->format('Y');
    }

    /**
     * query for get bphtb service
     */
    public function queryGetBphtbService($nop, $ntpd)
    {
        /**
         * check NOP
         */
        $checkNop = $this->secondDb->table('STS_History')->where('No_Pokok_WP', $nop)->first();
        if (is_null($checkNop)) :
            throw new \Exception('NOP tidak ditemukan');
        endif;

        /**
         * check NTPD
         */
        $checkNtpdSTS = $this->secondDb->table('STS_History')->where('Kode_Pengesahan', $ntpd)->first();
        $checkNtpdBriva = $this->thirdDb->table('briva_report')->whereRaw('CONCAT(ID,brivaNo) = "' . $ntpd . '"')->first();
        if (is_null($checkNtpdSTS) && is_null($checkNtpdBriva)) :
            throw new \Exception('NTPD tidak ditemukan');
        endif;

        /**
         * checking data
         */
        $checkDataSTS = $this->secondDb->table('STS_History')
            ->select(
                'Nama_Pemilik',
                'Nilai AS pembayaran',
                DB::raw("CASE WHEN Status_Bayar = 0 THEN 'N' ELSE 'Y' END AS status"),
                DB::raw("CASE WHEN Status_Bayar = 0 THEN 'H' ELSE 'L' END AS jenisbayar"),
                DB::raw("DATE_FORMAT(Tgl_Bayar, '%d/%m/%Y') AS tglbayar")
            )
            ->where([
                'No_Pokok_WP' => $nop,
                'Kode_Pengesahan' => $ntpd
            ])
            ->first();
        $checkDataBriva = $this->thirdDb->table('briva_report')
            ->select(
                'nama as Nama_Pemilik',
                'amount AS pembayaran',
                DB::raw("'Y' AS status"),
                DB::raw("'L' AS jenisbayar"),
                DB::raw("DATE_FORMAT(paymentDate, '%d/%m/%Y') AS tglbayar")
            )
            ->whereRaw('CONCAT(ID,brivaNo) = "' . $ntpd . '"')
            ->first();

        if (is_null($checkDataSTS) && is_null($checkDataBriva)) :
            throw new \Exception('Data tidak ditemukan');
        endif;
        $checkData = is_null($checkDataSTS) ? $checkDataBriva : $checkDataSTS;

        /**
         * get data objek pajak
         */
        $getDatObjekPajak = $this->datObjekPajak->select(
            DB::raw("JALAN_OP AS alamat"),
            DB::raw("(SELECT NM_KECAMATAN FROM ref_kecamatan WHERE ref_kecamatan.KD_KECAMATAN = dat_objek_pajak.KD_KECAMATAN) AS kecamatan"),
            DB::raw("(SELECT NM_KELURAHAN FROM ref_kelurahan WHERE ref_kelurahan.KD_KELURAHAN = dat_objek_pajak.KD_KELURAHAN AND ref_kelurahan.KD_KECAMATAN = dat_objek_pajak.KD_KECAMATAN) AS kelurahan"),
            DB::raw("'BINJAI' AS kota")
        )
            ->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
            ->first();
        if (is_null($getDatObjekPajak)) :
            throw new \Exception('Data tidak ditemukan');
        endif;

        /**
         * get SPPT
         */
        $getSppt = $this->sppt->select('LUAS_BUMI_SPPT', 'LUAS_BNG_SPPT')
            ->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
            ->orderByDesc('THN_PAJAK_SPPT')
            ->first();
        if (is_null($getSppt)) :
            throw new \Exception('Data tidak ditemukan');
        endif;

        /**
         * get pelayanan bphtb
         */
        $getPelayananBphtb = $this->pelayananBphtb->select('nik')->where('nop', $nop)->first();
        $nik = is_null($getPelayananBphtb) ? null : $getPelayananBphtb->nik;

        $data = [
            "result" => [
                "NOP" => $nop,
                "NIK" => $nik,
                "NAMA" => $checkData->Nama_Pemilik,
                "ALAMAT" => $getDatObjekPajak->alamat,
                "KELURAHAN_OP" => $getDatObjekPajak->kelurahan,
                "KECAMATAN_OP" => $getDatObjekPajak->kecamatan,
                "KOTA_OP" => $getDatObjekPajak->kota,
                "LUASTANAH" => $getSppt->LUAS_BUMI_SPPT,
                "LUASBANGUNAN" => $getSppt->LUAS_BNG_SPPT,
                "PEMBAYARAN" => (int) $checkData->pembayaran,
                "STATUS" => $checkData->status,
                "TANGGAL_PEMBAYARAN" => $checkData->tglbayar,
                "NTPD" => $ntpd,
                "JENISBAYAR" => $checkData->jenisbayar
            ],
            "respon_code" => "OK"
        ];

        return $data;
    }

    /**
     * udpate bphtb for kabbid
     */
    public function bphtbService($uuidPelayananBphtb)
    {
        try {

            /**
             * check bphtb
             */
            $checkBphtb = $this->pelayananBphtb->select('id', 'nop', 'no_sts')->where('uuid_pelayanan_bphtb', $uuidPelayananBphtb)->first();
            if (is_null($checkBphtb)) :
                throw new \Exception('BPHTB tidak ditemukan');
            endif;

            /**
             * get NTPD
             */
            $briCustCode = $this->year . $this->nomorUrutBrivaBphtb($checkBphtb->nop, $checkBphtb->id);
            $checkNtpdSTS = $this->secondDb->table('STS_History')->select('Kode_Pengesahan')->where('No_STS', $checkBphtb->no_sts)->first();
            $checkNtpdBriva = $this->thirdDb->table('briva_report')->select(DB::raw('CONCAT(ID,brivaNo) AS ntpd'))->where('custCode', $briCustCode)->first();
            if (is_null($checkNtpdSTS) && is_null($checkNtpdBriva)) :
                throw new \Exception('NTPD tidak ditemukan');
            endif;

            /**
             * get bphtb data
             */
            $nop = $checkBphtb->nop;
            $ntpd = is_null($checkNtpdBriva) ? $checkNtpdSTS->Kode_Pengesahan : $checkNtpdBriva->ntpd;
            $data = $this->queryGetBphtbService($nop, $ntpd);

            /**
             * update status verification
             */
            $updateStatusVerification = $this->pelayananBphtb->where('uuid_pelayanan_bphtb', $uuidPelayananBphtb)->update(['status_verifikasi' => 5]);
            if (!$updateStatusVerification) :
                throw new \Exception('Gagal update status verifikasi');
            endif;

            $response  = $data;
        } catch (\Throwable $e) {
            $response  = ['respon_code' => $e->getMessage()];
        }

        return $response;
    }

    /**
     * getBPHTBService
     */
    public function getBPHTBService($request)
    {
        try {
            /**
             * input validation
             */
            $nop = $request->NOP;
            $ntpd = $request->NTPD;
            $data = $this->queryGetBphtbService($nop, $ntpd);

            $response  = $data;
        } catch (\Throwable $e) {
            $response  = ['respon_code' => $e->getMessage()];
        }

        return $response;
    }

    /**
     * getPBBService
     */
    public function getPBBService()
    {
        try {
            $data = [
                "NOP" => "317405000600924660",
                "NIK" => "317405000600924660 ",
                "NAMA_WP" => "CITRA GEMILANG NUSANTARA PT",
                "ALAMAT_OP" => "JL HAYAM WURUK BLOK/KAV/NO K UG A5-2 RT 000 RW 06",
                "KECAMATAN_OP" => "TAMAN SARI",
                "KELURAHAN_OP" => "MANGGA BESAR",
                "KOTA_OP" => "JAKARTA BARAT",
                "LUASTANAH_OP" => 0,
                "LUASBANGUNAN_OP" => 7,
                "NJOP_TANAH_OP" => 0,
                "NJOP_BANGUNAN_OP" => 48650000,
                "STATUS_TUNGGAKAN" => "100% Lunas"
            ];
            $response  = $data;
        } catch (\Throwable $e) {
            $response  = $this->error($e->getMessage());
        }

        return $response;
    }
}

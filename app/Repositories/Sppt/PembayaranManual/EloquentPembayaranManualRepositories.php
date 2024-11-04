<?php

namespace App\Repositories\Sppt\PembayaranManual;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

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

use App\Models\PembayaranSppt\PembayaranManual\PembayaranManual;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\Sppt\Sppt;
use App\Models\DatObjekPajak\DatObjekPajak;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Sppt\PembayaranManual\PembayaranManualRepositories;

class EloquentPembayaranManualRepositories implements PembayaranManualRepositories
{
    use Message, Response, Generator, Calculation;

    private $pembayaranManual;
    private $checkerHelpers;
    private $paginateHelpers;
    private $storage;
    private $pembayaranSppt;
    private $sppt;
    private $datObjekPajak;

    public function __construct(
        PembayaranManual $pembayaranManual,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers,
        PembayaranSppt $pembayaranSppt,
        Sppt $sppt,
        DatObjekPajak $datObjekPajak
    ) {
        /**
         * initialize model
         */
        $this->pembayaranManual = $pembayaranManual;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->sppt = $sppt;
        $this->datObjekPajak = $datObjekPajak;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->storage = path('pembayaran manual');
    }

    /**
     * store data
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {

            /**
             * save to pembayaran sppt
             */
            $nop = $request['nop'];
            $kdProvinsi = substr($nop, 0, 2);
            $kdDati2 = substr($nop, 2, 2);
            $kdKecamatan = substr($nop, 4, 3);
            $kdKelurahan = substr($nop, 7, 3);
            $kdBlok = substr($nop, 10, 3);
            $noUrut = substr($nop, 13, 4);
            $kdJnsOp = substr($nop, 17, 1);
            $denda = $request['tagihan_dibayar'] == $request['jumlah_tagihan'] ? 0 : $request['tagihan_dibayar'] - $request['jumlah_tagihan'];

            /**
             * check pembayaran manual
             */
            $checkPembayaranManual = $this->pembayaranManual
                ->where([
                    'nop' => $request['nop'],
                    'tahun' => $request['tahun']
                ])
                ->first();
            if (!is_null($checkPembayaranManual)) :
                throw new \Exception($this->outputMessage('exists', 'NOP ' . $request['nop'] . ' tahun ' . $request['tahun'] . ' di pembayaran manual'));
            endif;

            /**
             * check sppt
             */
            $dataSppt = $this->sppt->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
                ->where('THN_PAJAK_SPPT', $request['tahun']);
            $checkSppt = $dataSppt->first();
            if (is_null($checkSppt)) :
                throw new \Exception($this->outputMessage('not found', 'SPPT dengan NOP ' . $nop . ' tahun ' . $request['tahun']));
            endif;


            /**
             * check pembayaran sppt
             */
            $checkPembayaranSppt = $this->pembayaranSppt->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
                ->where('THN_PAJAK_SPPT', $request['tahun'])
                ->first();
            if (!is_null($checkPembayaranSppt)) :
                throw new \Exception($this->outputMessage('exists', 'NOP ' . $nop . ' tahun ' . $request['tahun'] . ' di pembayaran sppt'));
            endif;

            /**
             * bukti bayar
             */
            if (isset($_FILES['bukti_bayar'])) :
                $buktiBayarName = $_FILES['bukti_bayar']['name'];
                $buktiBayarTempName = $_FILES['bukti_bayar']['tmp_name'];
                $buktiBayarExt = explode('.', $buktiBayarName);
                $buktiBayarActualExt = strtolower(end($buktiBayarExt));
                $buktiBayarNew = Uuid::uuid4()->getHex() . "." . $buktiBayarActualExt;
                $buktiBayarDestination = $this->storage . '/' . $buktiBayarNew;
                if (!move_uploaded_file($buktiBayarTempName, $buktiBayarDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['bukti_bayar'] = $buktiBayarNew;
            endif;

            /**
             * save to pembayaran manual
             */
            $request['nama'] = $checkSppt->NM_WP_SPPT;
            $savePembayaranManual = $this->pembayaranManual->create($request);
            if (!$savePembayaranManual) :
                throw new \Exception($this->outputMessage('unsaved', 'pembayaran manual'));
            endif;

            /**
             * save pembayaran sppt
             */
            $inputPembayaranSppt = [
                'KD_PROPINSI'         => $kdProvinsi,
                'KD_DATI2'            => $kdDati2,
                'KD_KECAMATAN'        => $kdKecamatan,
                'KD_KELURAHAN'        => $kdKelurahan,
                'KD_BLOK'             => $kdBlok,
                'NO_URUT'             => $noUrut,
                'KD_JNS_OP'           => $kdJnsOp,
                'THN_PAJAK_SPPT'      => $request['tahun'],
                'PEMBAYARAN_SPPT_KE'  => 1,
                'KD_KANWIL_BANK'      => '01',
                'KD_KPPBB_BANK'       => '07',
                'KD_BANK_TUNGGAL'     => '01',
                'KD_BANK_PERSEPSI'    => '02',
                'KD_TP'               => '01',
                'DENDA_SPPT'          => $denda,
                'JML_SPPT_YG_DIBAYAR' => $request['tagihan_dibayar'],
                'TGL_PEMBAYARAN_SPPT' => $request['tanggal_bayar'],
                'TGL_REKAM_BYR_SPPT'  => $request['tanggal_bayar'],
                'NIP_REKAM_BYR_SPPT'  => authAttribute()['nip'],
                'KODE_PENGESAHAN'     => 'BJI' . $this->nomorPembayaranManual()
            ];
            $savePembayaranSppt = $this->pembayaranSppt->insert($inputPembayaranSppt);
            if (!$savePembayaranSppt) :
                throw new \Exception($this->outputMessage('unsaved', 'pembayaran SPPT'));
            endif;

            /**
             * update sppt
             */
            if ($checkSppt->STATUS_PEMBAYARAN_SPPT == 0) :
                $updateSppt = $dataSppt->update(['STATUS_PEMBAYARAN_SPPT' => 1]);
                if (!$updateSppt) :
                    throw new \Exception($this->outputMessage('update fail', 'SPPT'));
                endif;
            endif;

            /**
             * set response
             */
            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'pembayaran manual'));
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * autocomplete
     */
    public function autocomplete($nop, $tahun)
    {
        try {

            /**
             * get letak objek pajak
             */
            $getDatObjekPajak = $this->datObjekPajak->select(
                'JALAN_OP',
                'BLOK_KAV_NO_OP',
            )
                ->whereRaw('concat(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
                ->first();
            if (is_null($getDatObjekPajak)) :
                throw new \Exception($this->outputMessage('not found', 'Objek Pajak'));
            endif;

            /**
             * get nama wp from sppt
             */
            $getSppt = $this->sppt->select('NM_WP_SPPT', 'PBB_YG_HARUS_DIBAYAR_SPPT', 'TGL_JATUH_TEMPO_SPPT')
                ->whereRaw('concat(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $nop . '"')
                ->where('THN_PAJAK_SPPT', $tahun)
                ->first();
            if (is_null($getSppt)) :
                throw new \Exception($this->outputMessage('not found', 'SPPT'));
            endif;

            $data = [
                'nama' => $getSppt->NM_WP_SPPT,
                'alamat' => $getDatObjekPajak->JALAN_OP,
                'blok' => $getDatObjekPajak->BLOK_KAV_NO_OP,
                'jumlah_tagihan' => $getSppt->PBB_YG_HARUS_DIBAYAR_SPPT,
                'jatuh_tempo' => $getSppt->TGL_JATUH_TEMPO_SPPT
            ];

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
     * all record
     */
    public function data()
    {
        try {
            /**
             * data pembayaran manual
             */
            $data   = $this->pembayaranManual->select('nop', 'tahun', 'tanggal_bayar', 'nama', 'jumlah_tagihan', 'metode_pembayaran', 'tagihan_dibayar')
                ->selectRaw('CASE WHEN bukti_bayar IS NULL THEN NULL ELSE CONCAT("' . url($this->storage) . '/", bukti_bayar) END AS bukti_bayar')
                ->get();

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

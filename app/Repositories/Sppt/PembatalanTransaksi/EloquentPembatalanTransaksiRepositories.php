<?php

namespace App\Repositories\Sppt\PembatalanTransaksi;

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

use App\Models\PembayaranSppt\PembatalanTransaksi\PembatalanTransaksi;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\Sppt\Sppt;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Sppt\PembatalanTransaksi\PembatalanTransaksiRepositories;

class EloquentPembatalanTransaksiRepositories implements PembatalanTransaksiRepositories
{
    use Message, Response, Generator, Calculation;

    private $pembatalanTransaksi;
    private $checkerHelpers;
    private $paginateHelpers;
    private $pembayaranSppt;
    private $sppt;
    private $storage;

    public function __construct(
        PembatalanTransaksi $pembatalanTransaksi,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers,
        PembayaranSppt $pembayaranSppt,
        Sppt $sppt
    ) {
        /**
         * initialize model
         */
        $this->pembatalanTransaksi = $pembatalanTransaksi;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->sppt = $sppt;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->storage = path('pembatalan transaksi');
    }

    /**
     * store data
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {

            /**
             * check sppt
             */
            $dataSppt = $this->sppt->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $request['nop'] . '"')
                ->where([
                    'THN_PAJAK_SPPT' => $request['tahun'],
                    'STATUS_PEMBAYARAN_SPPT' => 1
                ]);
            if (is_null($dataSppt->first())) :
                throw new \Exception($this->outputMessage('unpaid', 'SPPT dengan NOP ' . $request['nop'] . ' tahun ' . $request['tahun']));
            endif;

            /**
             * bukti
             */
            if (isset($_FILES['bukti'])) :
                $buktiName = $_FILES['bukti']['name'];
                $buktiTempName = $_FILES['bukti']['tmp_name'];
                $buktiExt = explode('.', $buktiName);
                $buktiActualExt = strtolower(end($buktiExt));
                $buktiNew = Uuid::uuid4()->getHex() . "." . $buktiActualExt;
                $buktiDestination = $this->storage . '/' . $buktiNew;
                if (!move_uploaded_file($buktiTempName, $buktiDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['bukti'] = $buktiNew;
            endif;

            /**
             * update sppt
             */
            $updateSppt = $dataSppt->update(['STATUS_PEMBAYARAN_SPPT' => 0]);
            if (!$updateSppt) :
                throw new \Exception($this->outputMessage('unsaved', 'SPPT'));
            endif;

            /**
             * check pembayaran sppt
             */
            $checkPembayaranSppt = $this->pembayaranSppt->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $request['nop'] . '"')
                ->where('THN_PAJAK_SPPT', $request['tahun']);
            if (is_null($checkPembayaranSppt->first())) :
                throw new \Exception($this->outputMessage('not found', 'Pembayaran SPPT dengan NOP ' . $request['nop'] . ' tahun ' . $request['tahun'] . ' di pembayaran sppt'));
            endif;

            /**
             * delete pembayaran sppt
             */
            $deletePembayaranSppt = $checkPembayaranSppt->delete();
            if (!$deletePembayaranSppt) :
                throw new \Exception($this->outputMessage('undeleted', $request['nop']));
            endif;

            /**
             * check pembatalan transaksi
             */
            $checkPembalatanTransaksi = $this->pembatalanTransaksi
                ->where([
                    'nop' => $request['nop'],
                    'tahun' => $request['tahun']
                ])
                ->first();
            if (!is_null($checkPembalatanTransaksi)) :
                throw new \Exception($this->outputMessage('exists', 'NOP ' . $request['nop'] . ' tahun ' . $request['tahun'] . ' di pembatalan transaksi'));
            endif;

            /**
             * save to pembatalan transaksi
             */
            $savePembatalanTransaksi = $this->pembatalanTransaksi->create($request);
            if (!$savePembatalanTransaksi) :
                throw new \Exception($this->outputMessage('unsaved', 'pembatalan transaksi'));
            endif;

            /**
             * set response
             */
            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'pembatalan transaksi'));
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

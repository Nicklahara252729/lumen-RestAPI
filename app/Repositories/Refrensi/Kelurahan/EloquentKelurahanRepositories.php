<?php

namespace App\Repositories\Refrensi\Kelurahan;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Refrensi\Kelurahan\Kelurahan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Refrensi\Kelurahan\KelurahanRepositories;

class EloquentKelurahanRepositories implements KelurahanRepositories
{
    use Message, Response;

    private $kelurahan;
    private $checkerHelpers;
    private $thirdDb;
    private $kelurahanPAD;

    public function __construct(
        Kelurahan $kelurahan,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->kelurahan = $kelurahan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->thirdDb = DB::connection('third_mysql');
        $this->kelurahanPAD = $this->thirdDb->table('ref_kelurahan');
    }

    /**
     * all record
     */
    public function getAll()
    {
        try {
            /**
             * data peta blok
             */
            $data = $this->kelurahan->get();

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
     * all record by param
     */
    public function data($kdKecamatan)
    {
        try {
            /**
             * data peta blok
             */
            $data = $this->kelurahan->where(['KD_KECAMATAN' => $kdKecamatan])->get();

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
     * all record by param for PAD
     */
    public function dataPAD($kecamatanId)
    {
        try {
            /**
             * data peta blok
             */
            $data = $this->kelurahanPAD->select('no_kelurahan AS kelurahan_id', 'nm_kelurahan')->where(['kdkecamatan' => $kecamatanId])->get();

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

<?php

namespace App\Repositories\Refrensi\Kecamatan;

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

use App\Models\Refrensi\Kecamatan\Kecamatan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Refrensi\Kecamatan\KecamatanRepositories;

class EloquentKecamatanRepositories implements KecamatanRepositories
{
    use Message, Response;

    private $kecamatan;
    private $kecamatanPAD;
    private $checkerHelpers;
    private $thirdDb;

    public function __construct(
        Kecamatan $kecamatan,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->kecamatan = $kecamatan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->thirdDb = DB::connection('third_mysql');
        $this->kecamatanPAD = $this->thirdDb->table('ref_kecamatan');
    }

    /**
     * all record
     */
    public function data()
    {
        try {
            /**
             * data peta blok
             */
            $data = $this->kecamatan->get();

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
     * all record for PAD
     */
    public function dataPAD()
    {
        try {
            /**
             * data kecamatan
             */
            $data = $this->kecamatanPAD->select('kdkecamatan AS kecamatan_id', 'nm_kecamatan')->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

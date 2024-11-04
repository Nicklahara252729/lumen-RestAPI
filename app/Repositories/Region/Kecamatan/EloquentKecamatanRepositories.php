<?php

namespace App\Repositories\Region\Kecamatan;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Region\Kecamatan\Kecamatan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import repositories
 */

use App\Repositories\Region\Kecamatan\KecamatanRepositories;

class EloquentKecamatanRepositories implements KecamatanRepositories
{
    use Message, Response;

    private $kecamatan;
    private $checkerHelpers;

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
    }

    /**
     * all record
     */
    public function data($idKabupaten)
    {
        try {
            /**
             * data kecamatan
             */
            $data = $this->kecamatan->where(["id_kabupaten" => $idKabupaten])->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by uuid
     */
    public function get($param)
    {
        try {
            $getKecamatan = $this->checkerHelpers->kecamatanChecker([["id_kecamatan" => $param], ["nama_kecamatan" => str_replace("%20", " ", $param)]]);
            if (is_null($getKecamatan)) :
                throw new \Exception($this->outputMessage('not found', 'kecamatan'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getKecamatan);
            endif;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

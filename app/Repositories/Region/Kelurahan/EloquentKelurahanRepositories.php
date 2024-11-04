<?php

namespace App\Repositories\Region\Kelurahan;

/**
 * import component
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Region\Kelurahan\Kelurahan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Region\Kelurahan\KelurahanRepositories;

class EloquentKelurahanRepositories implements KelurahanRepositories
{
    use Message, Response;

    private $kelurahan;
    private $checkerHelpers;

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
    }

    /**
     * all record
     */
    public function data($idKecamatan)
    {
        try {
            /**
             * data kelurahan
             */
            $data = $this->kelurahan->where(["id_kecamatan" => $idKecamatan])->get();
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
            $getKelurahan = $this->checkerHelpers->kelurahanChecker([["id_kelurahan" => $param], ["nama_kelurahan" => str_replace("%20", " ", $param)]]);
            if (is_null($getKelurahan)) :
                throw new \Exception($this->outputMessage('not found', 'bidang'));
            else :                
                $response  = $this->successData($this->outputMessage('data', 1), $getKelurahan);
            endif;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

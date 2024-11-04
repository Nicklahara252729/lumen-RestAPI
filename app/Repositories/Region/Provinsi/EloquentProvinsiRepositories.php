<?php

namespace App\Repositories\Region\Provinsi;

/**
 * import component
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Region\Provinsi\Provinsi;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import repositories
 */

use App\Repositories\Region\Provinsi\ProvinsiRepositories;

class EloquentProvinsiRepositories implements ProvinsiRepositories
{
    use Message, Response;

    private $provinsi;
    private $checkerHelpers;

    public function __construct(
        Provinsi $provinsi,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->provinsi = $provinsi;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
    }

    /**
     * all record
     */
    public function data()
    {
        try {
            /**
             * data provinsi
             */
            $data = $this->provinsi->get();
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
            /**
             * helpers
             */

            $getProvinsi = $this->checkerHelpers->provinsiChecker([["id_provinsi" => $param], ["nama_provinsi" => str_replace("%20", " ", $param)]]);
            if (is_null($getProvinsi)) :
                throw new \Exception($this->outputMessage('not found', 'provinsi'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getProvinsi);
            endif;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

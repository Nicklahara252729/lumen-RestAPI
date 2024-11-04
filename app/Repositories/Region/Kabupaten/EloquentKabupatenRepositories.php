<?php

namespace App\Repositories\Region\Kabupaten;

/**
 * import component
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Region\Kabupaten\Kabupaten;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Region\Kabupaten\KabupatenRepositories;

class EloquentKabupatenRepositories implements KabupatenRepositories
{
    use Message, Response;

    private $kabupaten;
    private $checkerHelpers;

    public function __construct(
        Kabupaten $kabupaten,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->kabupaten = $kabupaten;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
    }

    /**
     * all record
     */
    public function data($idProvinsi)
    {
        try {
            /**
             * data kabupaten
             */
            $data = $this->kabupaten->where(["id_provinsi" => $idProvinsi])->get();
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
            $getKabupaten = $this->checkerHelpers->kabupatenChecker([["id_kabupaten" => $param], ["nama_kabupaten" => str_replace("%20", " ", $param)]]);
            if (is_null($getKabupaten)) :
                throw new \Exception($this->outputMessage('not found', 'kabupaten'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getKabupaten);
            endif;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

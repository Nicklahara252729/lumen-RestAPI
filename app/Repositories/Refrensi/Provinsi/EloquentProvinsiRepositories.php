<?php

namespace App\Repositories\Refrensi\Provinsi;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Refrensi\Provinsi\Provinsi;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Refrensi\Provinsi\ProvinsiRepositories;

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
             * data peta blok
             */
            $data = $this->provinsi->get();

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

<?php

namespace App\Repositories\Refrensi\Blok;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Refrensi\PetaBlok\PetaBlok;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Refrensi\Blok\BlokRepositories;

class EloquentBlokRepositories implements BlokRepositories
{
    use Message, Response;

    private $petaBlok;
    private $checkerHelpers;

    public function __construct(
        PetaBlok $petaBlok,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->petaBlok = $petaBlok;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
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
            $data = $this->petaBlok->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * all record by param
     */
    public function data($kdKecamatan, $kdKelurahan)
    {
        try {
            /**
             * data peta blok
             */
            $data = $this->petaBlok->where(['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan])->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

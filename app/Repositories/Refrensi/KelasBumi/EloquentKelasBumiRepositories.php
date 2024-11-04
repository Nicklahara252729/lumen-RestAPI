<?php

namespace App\Repositories\Refrensi\KelasBumi;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Refrensi\KelasTanah\KelasTanah;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Refrensi\KelasBumi\KelasBumiRepositories;

class EloquentKelasBumiRepositories implements KelasBumiRepositories
{
    use Message, Response;

    private $kelasTanah;
    private $checkerHelpers;

    public function __construct(
        KelasTanah $kelasTanah,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->kelasTanah = $kelasTanah;

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
            $data = $this->kelasTanah->where(['THN_AKHIR_KLS_TANAH' => globalAttribute()['thnAkhirKelasTanah']])->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

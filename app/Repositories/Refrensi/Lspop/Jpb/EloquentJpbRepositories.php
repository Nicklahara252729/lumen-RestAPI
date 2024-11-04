<?php

namespace App\Repositories\Refrensi\Lspop\Jpb;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Refrensi\Jpb\Jpb;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Refrensi\Lspop\Jpb\JpbRepositories;

class EloquentJpbRepositories implements JpbRepositories
{
    use Message, Response;

    private $jpb;
    private $checkerHelpers;

    public function __construct(
        Jpb $jpb,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->jpb = $jpb;

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
             * data jpb
             */
            $data = $this->jpb->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

<?php

namespace App\Repositories\Refrensi\Pekerjaan;

/**
 * import component
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Refrensi\Pekerjaan\Pekerjaan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Refrensi\Pekerjaan\PekerjaanRepositories;

class EloquentPekerjaanRepositories implements PekerjaanRepositories
{
    use Message, Response;

    private $pekerjaan;
    private $checkerHelpers;

    public function __construct(
        Pekerjaan $pekerjaan,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->pekerjaan = $pekerjaan;

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
             * data pekerjaan
             */
            
            $data = $this->pekerjaan->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by parameter
     */
    public function get($param)
    {
        try {
            /**
             * helpers
             */

            $getPekerjaan = $this->checkerHelpers->pekerjaanChecker([["kode" => $param], ['nama' => str_replace("%20", " ", $param)]]);
            if (is_null($getPekerjaan)) :
                throw new \Exception("Data not found");
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getPekerjaan);
            endif;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

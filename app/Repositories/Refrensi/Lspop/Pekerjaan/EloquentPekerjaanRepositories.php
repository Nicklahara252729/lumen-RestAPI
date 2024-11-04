<?php

namespace App\Repositories\Refrensi\Lspop\Pekerjaan;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Refrensi\LspopPekerjaan\LspopPekerjaan;
use App\Models\Refrensi\LspopPekerjaanKegiatan\LspopPekerjaanKegiatan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Refrensi\Lspop\Pekerjaan\PekerjaanRepositories;

class EloquentPekerjaanRepositories implements PekerjaanRepositories
{
    use Message, Response;

    private $lspopPekerjaan;
    private $lspopPekerjaanKegiatan;
    private $checkerHelpers;

    public function __construct(
        LspopPekerjaan $lspopPekerjaan,
        LspopPekerjaanKegiatan $lspopPekerjaanKegiatan,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->lspopPekerjaan = $lspopPekerjaan;
        $this->lspopPekerjaanKegiatan = $lspopPekerjaanKegiatan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
    }

    /**
     * all record
     */
    public function data($namaPekerjaan)
    {
        try {
            $getPekerjaan = $this->checkerHelpers->lspopPekerjaanChecker(['NM_PEKERJAAN' => $namaPekerjaan]);
            if (is_null($getPekerjaan)) :
                throw new \Exception($this->outputMessage('not found', 'pekerjaan'));
            endif;

            $data = $this->lspopPekerjaanKegiatan->where("KD_PEKERJAAN", $getPekerjaan->KD_PEKERJAAN)->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

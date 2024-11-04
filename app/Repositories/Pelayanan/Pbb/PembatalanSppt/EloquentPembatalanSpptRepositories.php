<?php

namespace App\Repositories\Pelayanan\Pbb\PembatalanSppt;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Generator;
use App\Traits\Calculation;

/**
 * import models
 */

use App\Models\Sppt\Sppt;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Pelayanan\Pbb\PembatalanSppt\PembatalanSpptRepositories;

class EloquentPembatalanSpptRepositories implements PembatalanSpptRepositories
{
    use Message, Response, Generator, Calculation;

    private $checkerHelpers;
    private $paginateHelpers;
    private $sppt;

    public function __construct(
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers,
        Sppt $sppt
    ) {
        /**
         * initialize model
         */
        $this->sppt = $sppt;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;
    }

    /**
     * store data
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $deleteSppt = $this->sppt->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $request['nop'] . '"')
                ->where(['THN_PAJAK_SPPT' => $request['tahun']])
                ->delete();
            if (!$deleteSppt) :
                throw new \Exception($this->outputMessage('undeleted', 'SPPT'));
            endif;

            /**
             * set response
             */
            DB::commit();
            $response = $this->success($this->outputMessage('deleted', 'SPPT'));
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

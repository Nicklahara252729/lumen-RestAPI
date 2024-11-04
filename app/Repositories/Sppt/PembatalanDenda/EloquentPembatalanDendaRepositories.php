<?php

namespace App\Repositories\Sppt\PembatalanDenda;

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
use App\Models\PembayaranSppt\PembatalanDenda\PembatalanDenda;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Sppt\PembatalanDenda\PembatalanDendaRepositories;

class EloquentPembatalanDendaRepositories implements PembatalanDendaRepositories
{
    use Message, Response, Generator, Calculation;

    private $checkerHelpers;
    private $paginateHelpers;
    private $sppt;
    private $pembatalanDenda;

    public function __construct(
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers,
        Sppt $sppt,
        PembatalanDenda $pembatalanDenda
    ) {
        /**
         * initialize model
         */
        $this->sppt = $sppt;
        $this->pembatalanDenda = $pembatalanDenda;

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

            /**
             * check pembatalan denda
             */
            $checkPembalatanDenda = $this->pembatalanDenda
                ->where([
                    'nop' => $request['nop'],
                    'tahun' => $request['tahun']
                ])
                ->first();
            if (!is_null($checkPembalatanDenda)) :
                throw new \Exception($this->outputMessage('exists', 'NOP ' . $request['nop'] . ' tahun ' . $request['tahun'] . ' di pembatalan denda'));
            endif;

            /**
             * save to pembatalan denda
             */
            $savePembatalanDenda = $this->pembatalanDenda->create($request);
            if (!$savePembatalanDenda) :
                throw new \Exception($this->outputMessage('unsaved', 'pembatalan denda'));
            endif;

            /**
             * check sppt
             */
            $updateSppt = $this->sppt->whereRaw('CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = "' . $request['nop'] . '"')
                ->where(['THN_PAJAK_SPPT' => $request['tahun']])
                ->update(['TGL_JATUH_TEMPO_SPPT' => $request['jatuh_tempo']]);
            if (!$updateSppt) :
                throw new \Exception($this->outputMessage('unsaved', 'SPPT'));
            endif;

            /**
             * set response
             */
            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'pembatalan denda'));
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

<?php

namespace App\Repositories\Setting\Layanan;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Layanan\Layanan\Layanan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Setting\Layanan\LayananRepositories;

class EloquentLayananRepositories implements LayananRepositories
{
    use Message, Response;

    private $layanan;
    private $checkerHelpers;

    public function __construct(
        Layanan $layanan,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->layanan = $layanan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
    }

    /**
     * update status
     */
    public function updateStatus($request, $uuidLayanan)
    {
        DB::beginTransaction();
        try {
            /**
             * helpers
             */
            $getLayanan = $this->checkerHelpers->layananChecker(["uuid_layanan" => $uuidLayanan]);
            $layanan = isset($getLayanan->layanan) ? $getLayanan->layanan : null;
            if (is_null($getLayanan)) :
                throw new \Exception($this->outputMessage('not found', 'layanan'));
            endif;

            /**
             * update data
             */
            $updateLayanan = $this->layanan->where(['uuid_layanan' => $uuidLayanan])->update(['status' => (float)$request['status']]);
            if (!$updateLayanan) :
                throw new \Exception($this->outputMessage('update fail', 'status ' . $layanan));
            else :
                DB::commit();
                $response = $this->success($this->outputMessage('updated', 'status ' . $layanan));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        /**
         * send response to controller
         */
        return $response;
    }
}

<?php

namespace App\Repositories\Refrensi\JenisPajak;

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
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Refrensi\JenisPajak\JenisPajakRepositories;

class EloquentJenisPajakRepositories implements JenisPajakRepositories
{
    use Message, Response;

    private $thirdDb;
    private $jenisPajak;
    private $checkerHelpers;

    public function __construct(
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->thirdDb = DB::connection('third_mysql');
        $this->jenisPajak = $this->thirdDb->table('x_rekening');
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
            $data = $this->jenisPajak->select('idrek AS pajak_id', DB::raw("REPLACE(REPLACE(nama_rekening, '\r', ''), '\n', '') AS jenis_pajak"))
                ->where('tahun', 2023)->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

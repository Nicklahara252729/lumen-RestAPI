<?php

namespace App\Repositories\Report\Bphtb\Skpdkb;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Converter;
use App\Traits\Calculation;

/**
 * import models
 */

use App\Models\Skpdkb\Skpdkb;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Report\Bphtb\Skpdkb\ReportSkpdkbRepositories;

class EloquentReportSkpdkbRepositories implements ReportSkpdkbRepositories
{
    use Message, Response, Converter, Calculation;

    private $skpdkb;
    private $checkerHelpers;

    public function __construct(
        CheckerHelpers $checkerHelpers,
        Skpdkb $skpdkb
    ) {
        /**
         * initialize model
         */
        $this->skpdkb = $skpdkb;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
    }

    /**
     * data
     */
    public function data($statusBayar, $startDate, $endDate)
    {
        try {
            /**
             * data skpdkb
             */
            $statusBayar = $statusBayar == 'all' ? [0, 1] : [$statusBayar];
            $data = $this->skpdkb->select([
                'uuid_skpdkb',
                'no_skpdkb',
                'sspd',
                'nop',
                'nama_wp_2',
                'total_skpdkb',
                'name as inserted_by',
                'date_update',
                'status_bayar',
            ])
                ->join('pelayanan_bphtb', 'skpdkb.sspd', '=', 'pelayanan_bphtb.no_registrasi')
                ->join('users', 'skpdkb.USER_UPDATE', '=', 'users.uuid_user')
                ->whereIn('status_bayar', $statusBayar);
            if (!empty($startDate) && !empty($endDate)) :
                $data = $data->whereRaw("DATE(date_update) between '" . $startDate . "' AND '" . $endDate."'");
            endif;
            $data = $data->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}

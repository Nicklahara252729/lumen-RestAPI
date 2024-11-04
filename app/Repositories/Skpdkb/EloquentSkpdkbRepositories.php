<?php

namespace App\Repositories\Skpdkb;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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

use App\Models\Skpdkb\Skpdkb;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import repositories
 */

use App\Repositories\Skpdkb\SkpdkbRepositories;

class EloquentSkpdkbRepositories implements SkpdkbRepositories
{
    use Message, Response, Generator, Calculation;

    private $skpdkb;
    private $checkerHelpers;
    private $paginateHelpers;
    private $datetime;
    private $secondDb;
    private $year;

    public function __construct(
        Skpdkb $skpdkb,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers
    ) {
        /**
         * initialize model
         */
        $this->skpdkb = $skpdkb;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->secondDb = DB::connection('second_mysql');
        $this->year = Carbon::now()->format('Y');
    }

    /**
     * all record
     */
    public function data()
    {
        try {
            /**
             * data skpdkb
             */
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
                ->get();

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * store data tunggakan
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {

            /**
             * set request SKPDKB
             */
            $request['uuid_skpdkb'] = (string) Str::orderedUuid();
            $request['kode_bayar'] = $this->noSts();
            $request['no_skpdkb'] = $this->noSkpdkb();
            $request['npopkp'] = $request['npop'] - $request['npoptkp'];
            $request['status_bayar'] = 0;
            $request['date_update'] = $this->datetime;
            $request['total_skpdkb'] = $this->totalSkpdkb($request['npopkp']);
            $request['user_update'] = authAttribute()['id'];

            /**
             * check bphtb data
             */
            $checkBphtb = $this->checkerHelpers->pelayananBphtbChecker(['no_registrasi' => $request['sspd']]);
            if (is_null($checkBphtb)) :
                throw new \Exception($this->outputMessage('not found', 'BPHTB'));
            endif;

            /**
             * save to skpdkb
             */
            $saveSkpdkb = $this->skpdkb->insert($request);
            if (!$saveSkpdkb) :
                throw new \Exception($this->outputMessage('unsaved', 'SKPDKB'));
            endif;

            /**
             * set request STS
             */
            $stsValue = [
                'Tahun' => $this->year,
                'No_STS' => $request['kode_bayar'],
                'Tgl_STS' => $this->datetime,
                'No_NOP' => $this->nopStsBphtb(),
                'No_Pokok_WP' => $checkBphtb->nop,
                'Nama_Pemilik' => $checkBphtb->nama_wp_2,
                'Alamat_Pemilik' => $checkBphtb->alamat_wp_2,
                'Jn_Pajak' => globalAttribute()['stsBphtb'],
                'Nm_Pajak' => 'BPHTB',
                'Nilai' => $request['total_skpdkb']
            ];

            /**
             * save to sts history
             */
            $saveSts = $this->secondDb->table('STS_History')->insert($stsValue);
            if (!$saveSts) :
                throw new \Exception($this->outputMessage('unsaved', 'STS History'));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'SKPDKB dengan nomor SSPD ' . $request['sspd']));
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

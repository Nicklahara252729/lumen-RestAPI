<?php

namespace App\Repositories\Bidang\Bidang;

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

use App\Models\Bidang\Bidang\Bidang;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Bidang\Bidang\BidangRepositories;

class EloquentBidangRepositories implements BidangRepositories
{
    use Message, Response;

    private $signature;
    private $bidang;
    private $checkerHelpers;

    public function __construct(
        Bidang $bidang,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->bidang = $bidang;

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
             * data bidang
             */
            $data = $this->bidang->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by uuid
     */
    public function get($uuidBidang)
    {
        try {
            /**
             * get single data
             */

            $getBidang = $this->checkerHelpers->bidangChecker(["uuid_bidang" => $uuidBidang]);
            if (is_null($getBidang)) :
                throw new \Exception($this->outputMessage('not found', 'bidang'));
            endif;

            $response  = $this->successData($this->outputMessage('data', 1), $getBidang);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * store data to db
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            /**
             * save data bidang
             */
            $saveData = $this->bidang->create($request);
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', $request['nama_bidang']));
            endif;

            DB::commit();
            $response  = $this->success($this->outputMessage('saved', $request['nama_bidang']));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * update data to db
     */
    public function update($request, $uuidBidang)
    {
        DB::beginTransaction();
        try {
            /**
             * validation data
             */
            $getBidang = $this->checkerHelpers->bidangChecker(["uuid_bidang" => $uuidBidang]);
            if (is_null($getBidang)) :
                throw new \Exception($this->outputMessage('not found', 'bidang'));
            endif;

            /**
             * update data
             */
            $updateBidang = $this->bidang->where(['uuid_bidang' => $uuidBidang])->update($request);
            if (!$updateBidang) :
                throw new \Exception($this->outputMessage('update fail', $request['nama_bidang']));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', $request['nama_bidang']));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * delete data from db
     */
    public function delete($uuidBidang)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData = $this->checkerHelpers->bidangChecker(["uuid_bidang" => $uuidBidang]);
            $bidang  = is_null($getData) ? null : $getData->nama_bidang;
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'bidang'));
            endif;

            /**
             * deleted data
             */
            $delete = $this->bidang->where('uuid_bidang', $uuidBidang)->delete();
            if (!$delete) :
                throw new \Exception($this->outputMessage('undeleted', $bidang));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('deleted', $bidang));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
